<?php

class Best2pay extends Core
{
    /**
     * Тестовые карты
     *
        2200200111114591, 05/2022, 426 // отмена
        5570725111081379, 05/2022, 415 с 3ds // проведена
        4809388889655340, 05/2022, 195 // проведена
     */
    
    private $url = 'https://test.best2pay.net/';
    private $currency_code = 643;
    private $sectors;
    private $fee = 0; // сумма комиссии при проведении платежа
    private $min_fee = 0; // сумма минимальной комиссии при проведении платежа
    
    /*
Sector ID: 9282 ООО МКК "Русское кредитное общество" (rucred.ru) (СМЭВ)
Sector ID: 9283 ООО МКК "Русское кредитное общество" (rucred.ru) (token)
Sector ID: 9287 ООО МКК "Русское кредитное общество" (rucred.ru) (PurchasebySectorCard)
Sector ID: 9288 ООО МКК "Русское кредитное общество" (rucred.ru) (P2PCredit)
Sector ID: 9285 ООО МКК "Русское кредитное общество" (rucred.ru) (C2A)    
    */
    private $passwords = array(
        '3158' => 'test', // С2А
        '3159' => 'test', // P2PCredit
        '3160' => 'test', // Ecom
        '3157' => 'test', // token
        '3721' => 'test', // PurchaseBySectorCard
        
        '9285' => 's02V01I1', // (C2A)    
        '9288' => 'ce3XY81', // (P2PCredit)
        '9282' => 'RWt5U82X807', // (СМЭВ)
        '9283' => '8630FtF2', // (token)
        '9287' => 'Dr03924', // (PurchasebySectorCard)
    );

    public function __construct()
    {
        if ($this->settings->b2p_mode == 'work')
        {
            $this->sectors = array(
                'ISSUANCE' => '9287', 
                'PAYMENT' => '9285',
            );
        }
        else
        {
            $this->sectors = array(
                'ISSUANCE' => '3721', 
                'PAYMENT' => '3158',
            );            
        }
        
        parent::__construct();
    }
    
    public function get_sectors()
    {
        return $this->sectors;
    }
    
    public function get_sector($type)
    {
        return isset($this->sectors[$type]) ? $this->sectors[$type] : null;
    }
    
    
    /**
     * Best2pay::get_payment_link()
     * 
     * Метод возвращает ссылку для оплаты любой картой
     * 
     * @param int $amount - Сумма платежа в копейках
     * @param string $contract_id - Номер договора
     * @return string
     */
    public function get_payment_link($amount, $contract_id, $prolongation = 0, $card_id = 0, $sms = NULL)
    {
        $sector = $this->sectors['PAYMENT'];
        $password = $this->passwords[$sector];            
        
        $fee = ceil(max($this->min_fee, floatval($amount * $this->fee)));
        
        if (!($contract = $this->contracts->get_contract($contract_id)))
            return false;
        
        if (!($user = $this->users->get_user((int)$contract->user_id)))
            return false;
        
        $description = 'Оплата по договору '.$contract->number;
        
        // регистрируем оплату
        $data = array(
            'sector' => $sector,
            'amount' => $amount ,
            'currency' => $this->currency_code,
            'reference' => $contract->id,
            'description' => $description,
            'mode' => 1,
            'fee' => $fee,
            'url' => $this->config->root_url.'/ajax/best2pay.php?action=callback',
            'phone' => $user->phone_mobile,
            'fio' => $user->lastname.' '.$user->firstname.' '.$user->patronymic,
            'contract' => $contract->number,
//            'get_token' => 1,
        );
        
        $data['signature'] = $this->get_signature(array(
            $data['sector'], 
            $data['amount'], 
            $data['currency'], 
            $password
        ));
        
        $b2p_order_id = $this->send('Register', $data);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($b2p_order_id);echo '</pre><hr />';        


        $transaction_id = $this->transactions->add_transaction(array(
            'user_id' => $contract->user_id,
            'amount' => $amount,
            'sector' => $sector,
            'register_id' => $b2p_order_id,
            'reference' => $contract->id,
            'description' => $description,
            'created' => date('Y-m-d H:i:s'),
            'prolongation' => $prolongation,
            'commision_summ' => $fee / 100,
            'sms' => empty($sms) ? NULL : $sms,
            'body' => serialize($data),
        ));
        // получаем длинную ссылку на оплату
        $data = array(
            'sector' => $sector,
            'id' => $b2p_order_id,            
        );
        if (!empty($card_id))
        {
            $card = $this->cards->get_card((int)$card_id);
            $data['token'] = $card->token;
//            $data['pan_token'] = $card->pan;
            $data['action'] = 'pay';
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($data, $card);echo '</pre><hr />';        
        $data['signature'] = $this->get_signature(array($sector, $b2p_order_id, $password));

        $link = $this->url.'webapi/Purchase?'.http_build_query($data);
    
        return $link;
    }
        
    /**
     * Best2pay::issuance()
     * Переводит сумму займа на карту клиенту
     * @param integer $contract_id
     * @return string - статус перевода COMPLETE при успехе или пустую строку
     */
    public function issuance($order_id)
    {
        $sector = $this->sectors['ISSUANCE'];
        $password = $this->passwords[$sector];

        if (!($order = $this->orders->get_order($order_id))) {
            return ['error' => 'Не найдена заявка '.$order_id];
        }
        
        if ($order->status != 4) {
            return ['error' => 'Заявка не находится в статусе подписана'];
        }
        
        if (!($requisite = $this->requisites->get_requisite($order->requisite_id))) {
            return ['error' => 'Не найдены реквизиты получателя'];
        }
        
        if (!($settlement = $this->OrganisationSettlements->get_settlement($order->settlement_id))) {
            return ['error' => 'Не найдены организация отправителя'];
        }

        if (!($company = $this->companies->get_company($order->company_id))) {
            return ['error' => 'Не найдены компания отправителя'];
        }
        $this->orders->update_order($order->order_id, array('status' => 9));

        $description = 'Перечисление средств по договору микрозайма №'.$order->uid.' от '.date('d.m.Y', strtotime($order->date)).' г., Сумма '.$order->amount.' Без налога (НДС)';

        $register_data = [
            'sector' => $sector,
            'reference' => $order_id,
            'amount' => $order->amount * 100,
            'currency' => $this->currency_code,
            'email' => $order->email,
            'phone' => $order->phone_mobile,
            'description' => $description,
        ];
        $register_data['signature'] = $this->get_signature(array(
            $register_data['sector'],
            $register_data['amount'],
            $register_data['currency'],
            $password
        ));
        
        $register_response = $this->send('Register', $register_data, 'webapi');

        $register_xml = simplexml_load_string($register_response);
        $register_id = (string)$register_xml->id;
        $register_status = (string)$register_xml->state;

        if (!empty($register_id))
        {
            $issuance_id = $this->issuances->add_issuance(array(
                'order_id' => $order->order_id,
                'user_id' => $order->user_id,
                'date' => date('Y-m-d H:i:s'),
                'body' => $register_data,
                'sector' => $sector,
                'register_id' => $register_id,         
                'status' => $register_status       
            ));
            
            $data = [
                'sector' => $sector,
                'id' => $register_id,
                'country' => 'RU',
                'bank_name' => $settlement->name,
                'fio' => $order->lastname.' '.$order->firstname.' '.$order->patronymic, 
                'acc_number' => $requisite->number,
                'P008-1' => $company->name, //Наименование Плательщика.  
                'P008-2' => $company->jur_address, //Адрес Плательщика.  
                'P014' => $requisite->bik, // БИК получателя (ровно 9 цифр).  
                'P016' => $requisite->holder, // Наименование Получателя.  
                'P017' => $requisite->correspondent_acc, // Счет получателя (ровно 20 цифр).  
                'P020' => '1', // Очередность платежа (ровно 1 цифра).  
                'P024' => $description, // Назначение платежа.  
                'P060' => $company->inn ?? 0, // ИНН Плательщика (10 или 12 цифр). Если отсутствует —проставьте 0.   
                'P061' => $order->inn ?? 0, // ИНН Получателя (10 или 12 цифр). Если отсутствует —проставьте 0.  
                'P022' => 0, // УИН (20 или 25 цифр). Если отсутствует —проставьте 0.  
                'P101' => 14, // Статус Плательщика.  
                'P102' => $company->kpp, // КПП Плательщика (9 цифр). Если отсутствует —проставьте 0.  
                'P103' => 0, // КПП Получателя (9 цифр). Если отсутствует —проставьте 0.  
            ];
            
            $data['signature'] = $this->get_signature(array(
                $data['sector'],
                $data['id'],
                $password
            ));
            
            $response = $this->send('PurchaseBySectorCard', $data, 'webapi');
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($data, $response);echo '</pre><hr />';
            $xml = simplexml_load_string($response);
            $status = (string)$xml->order_state;
            
            if ($status == 'COMPLETED')
            {
                $this->orders->update_order($order->order_id, array('status' => 5));

                $this->issuances->update_issuance($issuance_id, [
                    'status' => $status,
                    'complete_date' => date('Y-m-d H:i:s'),
                    'response' => $response,
                    'body' => $data,
                    'operation_id' => (string)$xml->id,
                ]);

                return ['success' => 1, 'status' => $status];
            }
            else
            {
                $this->orders->update_order($order->order_id, array('status' => 6));
                
                $xml_nodename = (string)$register_xml->getName();
                
                if ($xml_nodename === 'error') {
                    $this->issuances->update_issuance($issuance_id, [
                        'status' => 'ERROR',
                        'complete_date' => date('Y-m-d H:i:s'),
                        'response' => $response,
                        'body' => $data,
                    ]);
                    
                    return ['error' => 'Не удалось выдать: '. $register_xml->description];
                }

                    $this->issuances->update_issuance($issuance_id, [
                        'status' => $status,
                        'complete_date' => date('Y-m-d H:i:s'),
                        'response' => $response,
                        'body' => $data,
                    ]);

                return ['error' => 'Не удалось выдать'];
            }
        }
        else
        {
            $register_xml_nodename = (string)$register_xml->getName();
            if ($register_xml_nodename === 'error') {
                return ['error' => 'Заказ не удалось зарегистрировать: '. $register_xml->description];
            }
            return ['error' => 'Заказ не удалось зарегистрировать'];
        }

    }
    
    
    public function get_operation_info($sector, $register_id, $operation_id)
    {
        $password = $this->passwords[$sector];
               
        $data = array(
            'sector' => $sector,
            'id' => $register_id,
            'operation' => $operation_id,
            'get_token' => 1
        );
        $data['signature'] = $this->get_signature(array($sector, $register_id, $operation_id, $password));
        
        $info = $this->send('Operation', $data);
    
        return $info;
    }
        
    public function get_register_info($sector, $register_id, $get_token = 0)
    {
        $password = $this->passwords[$sector];
                
        $data = array(
            'sector' => $sector,
            'id' => $register_id,
            'mode' => 0,
            'get_token' => $get_token
        );
        $data['signature'] = $this->get_signature(array($sector, $register_id, $password));
        
        $info = $this->send('Order', $data);
    
        return $info;
    }
    
    
    private function send($method, $data, $type = 'webapi')
    {
        $url = $this->url.$type.'/'.$method;
        $string_data = http_build_query($data);
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $string_data);

        $resp = curl_exec($curl);
        curl_close($curl);
        
        $this->soap1c->logging(__METHOD__, $url, $data, $resp, 'b2p.txt');
        
        if ($error = curl_error($curl))
        {
            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($error);echo '</pre><hr />';
                    
        }        
                        
        return $resp;
    }
    
    private function get_signature($data)
    {
        $str = '';
        foreach ($data as $item)
            $str .= $item;
        
        $md5 = md5($str);
        $signature = base64_encode($md5);
        
        return $signature;
    }
    
    public function get_reason_code_description($code)
    {
        $descriptions = array(
            2 => 'Неверный срок действия Банковской карты. <br />Платёж отклонён. Возможные причины: недостаточно средств на счёте, были указаны неверные реквизиты карты, по Вашей карте запрещены расчёты через Интернет. Пожалуйста, попробуйте выполнить платёж повторно или обратитесь в Банк, выпустивший Вашу карту. ',
            3 => 'Неверный статус Банковской карты на стороне Эмитента. <br />Платёж отклонён. Пожалуйста, обратитесь в Банк, выпустивший Вашу карту. ',
            4 => 'Операция отклонена Эмитентом. <br />Платёж отклонён. Пожалуйста, обратитесь в Банк, выпустивший Вашу карту. ',
            5 => 'Операция недопустима для Эмитента. Платёж отклонён. Пожалуйста, обратитесь в Банк, выпустивший Вашу карту. ',
            6 => 'Недостаточно средств на счёте Банковской карты. <br />Платёж отклонён. Возможные причины: недостаточно средств на счёте, были указаны неверные реквизиты карты, по Вашей карте запрещены расчёты через Интернет. Пожалуйста, попробуйте выполнить платёж повторно или обратитесь в Банк, выпустивший Вашу карту. ',
            7 => 'Превышен установленный для ТСП лимит на сумму операций (дневной, недельный, месячный) или сумма операции выходит за пределы установленных границ. <br />Платёж отклонён. Пожалуйста, обратитесь в Контактный центр. ',
            8 => 'Операция отклонена по причине срабатывания системы предотвращения мошенничества. <br />Платёж отклонён. Пожалуйста, обратитесь в Контактный центр. ',
            9 => 'Заказ уже находится в процессе оплаты. Операция, возможно, задублировалась. <br />Платёж отклонён. Пожалуйста, обратитесь в Контактный центр. ',
            10 => 'Системная ошибка. <br />Платёж отклонён. Пожалуйста, обратитесь в Контактный центр. ',
            11 => 'Ошибка 3DS аутентификации. <br />Платёж отклонён. Пожалуйста, обратитесь в Контактный центр. ',
            12 => 'Указано неверное значение секретного кода карты. <br />Платёж отклонён. Возможные причины: недостаточно средств на счёте, были указаны неверные реквизиты карты, по Вашей карте запрещены расчёты через Интернет. Пожалуйста, попробуйте выполнить платёж повторно или обратитесь в Банк, выпустивший Вашу карту. ',
            13 => 'Операция отклонена по причине недоступности Эмитента и/или Банка- эквайрера. <br />Платёж отклонён. Пожалуйста, попробуйте выполнить платёж позднее или обратитесь в Контактный центр. ',
            14 => 'Операция отклонена оператором электронных денег. <br />Платёж отклонён. Пожалуйста, обратитесь в платёжную систему, электронными деньгами которой Вы пытаетесь оплатить Заказ. ',
            15 => 'BIN платёжной карты присутствует в черных списках. <br />Платёж отклонён. Пожалуйста, обратитесь в Контактный центр. ',
            16 => 'BIN 2 платёжной карты присутствует в черных списках. <br />Платёж отклонён. Пожалуйста, обратитесь в Контактный центр. ',
            0 => 'Операция отклонена по другим причинам. Требуется уточнение у ПЦ.<br />Платёж отклонён. Пожалуйста, попробуйте выполнить платёж позднее или обратитесь в Контактный центр. '
        );
        
        return isset($descriptions[$code]) ? $descriptions[$code] : '';
    }
    
    
    
    
    
}
