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
    
    private $sectors = array(
        'ISSUANCE' => '3721', 
    );
    
    private $passwords = array(
        '3158' => 'test', // С2А
        '3159' => 'test', // P2PCredit
        '3160' => 'test', // Ecom
        '3157' => 'test', // token
        '3721' => 'test', // PurchaseBySectorCard

    );

    public function __construct()
    {
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
            return ['error' => 1, 'message' => 'Не найдена заявка '.$order_id];
        }
        
        if ($order->status != 4) {
            return ['error' => 1, 'message' => 'Заявка не находится в статусе подписана'];
        }
        
        if (!($requisite = $this->requisites->get_requisite($order->requisite_id))) {
            return ['error' => 1, 'message' => 'Не найдены реквизиты получателя'];
        }
        
        if (!($settlement = $this->OrganisationSettlements->get_settlement($order->settlement_id))) {
            return ['error' => 1, 'message' => 'Не найдены организация отправителя'];
        }
//        $this->orders->update_order($order->order_id, array('status' => 9));

        $description = 'Выдача займа по договору ' . $order->uid;
        
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
                'P008-1' => '', //Наименование Плательщика.  
                'P008-2' => '', //Адрес Плательщика.  
                'P014' => $requisite->bik, // БИК получателя (ровно 9 цифр).  
                'P016' => $requisite->holder, // Наименование Получателя.  
                'P017' => $requisite->correspondent_acc, // Счет получателя (ровно 20 цифр).  
                'P020' => '', // Очередность платежа (ровно 1 цифра).  
                'P024' => $description, // Назначение платежа.  
                'P060' => 0, // ИНН Плательщика (10 или 12 цифр). Если отсутствует —проставьте 0.   
                'P061' => $order->inn ?? 0, // ИНН Получателя (10 или 12 цифр). Если отсутствует —проставьте 0.  
                'P022' => 0, // УИН (20 или 25 цифр). Если отсутствует —проставьте 0.  
                'P101' => 14, // Статус Плательщика.  
                'P102' => 0, // КПП Плательщика (9 цифр). Если отсутствует —проставьте 0.  
                'P103' => 0, // КПП Получателя (9 цифр). Если отсутствует —проставьте 0.  
                'P104' => '', // КБК Получателя.  
                'P105' => '', // ОКТМО (8 или 11 цифр).  
                'P106' => '', // Основание платежа.  
                'P107' => '', // Налоговый период.  
                'P108' => $order->uid, // Номер документа, который является основанием платежа, либо 0. 
                'P109' => date('Y.m.d'), // Дата документа (формат yyyy.MM.dd).  Если отсутствует —проставьте 0.  
                'P110' => '', // Тип документа. 
            ];
            
            $data['signature'] = $this->get_signature(array(
                $data['sector'],
                $data['id'],
                $password
            ));
            
            $response = $this->send('PurchaseBySectorCard', $data, 'webapi');
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($response);echo '</pre><hr />';
            $xml = simplexml_load_string($response);
            $status = (string)$xml->order_state;
            
            if ($status == 'APPROVED')
            {
                return ['success' => 1, 'status' => 'APPROVED', ];
                $this->issuances->update_issuance($issuance_id, [
                    'status' => $status,
                    'complete_date' => date('Y-m-d H:i:s'),
                    'response' => $response,
                    'body' => $data,
                ]);
            }
            else
            {
                
                if ($register_xml_nodename === 'error') {
                    $this->issuances->update_issuance($issuance_id, [
                        'status' => 'ERROR',
                        'complete_date' => date('Y-m-d H:i:s'),
                        'response' => $response,
                        'body' => $data,
                    ]);
                    
                    return ['error' => 1, 'message' => 'Не удалось выдать: '. $register_xml->description];
                }

                    $this->issuances->update_issuance($issuance_id, [
                        'status' => $status,
                        'complete_date' => date('Y-m-d H:i:s'),
                        'response' => $response,
                        'body' => $data,
                    ]);

                return ['error' => 1, 'message' => 'Не удалось выдать'];
            }
        }
        else
        {
            $register_xml_nodename = (string)$register_xml->getName();
            if ($register_xml_nodename === 'error') {
                return ['error' => 1, 'message' => 'Заказ не удалось зарегистрировать: '. $register_xml->description];
            }
            return ['error' => 1, 'message' => 'Заказ не удалось зарегистрировать'];
        }
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($register_response, $register_id, $response);echo '</pre><hr />';        
exit;        

        $p2pcredit = array(
            'contract_id' => $contract->id,
            'user_id' => $user->id,
            'date' => date('Y-m-d H:i:s'),
            'body' => $data,
            'sector' => $sector,
        );
        if ($p2pcredit_id = $this->add_p2pcredit($p2pcredit)) 
        {
            $response = $this->send('P2PCredit', $data, 'gateweb');
            
            $xml = simplexml_load_string($response);
            $status = (string)$xml->order_state;
            
            $this->update_p2pcredit($p2pcredit_id, array(
                'response' => $response,
                'status' => $status,
                'register_id' => (string)$xml->order_id,
                'operation_id' => (string)$xml->id,
                'complete_date' => date('Y-m-d H:i:s'),
            ));
    
    //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(htmlspecialchars($response));echo '</pre><hr />';
    
            return $status;
        }
        
        
        



        $action = "Register";
        $request_url = $best2pay_endpoint . $action;

        $best2pay_sector = (int)$this->config->best2pay_current_sector_id;

        $best2pay_password = $this->config->best2pay_sector3721_pass;

        $best2pay_amount = $order->amount;
        $best2pay_currency = $this->config->best2pay_currency;
        $best2pay_email = $order->email;
        $best2pay_phone = $order->phone_mobile;
        $best2pay_signature = base64_encode(md5($best2pay_sector . $best2pay_amount . $best2pay_currency . $best2pay_password));

        try {
            $ch = curl_init($request_url);
            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'sector' => $best2pay_sector,
                'reference' => $order_id,
                'amount' => $best2pay_amount,
                'currency' => $best2pay_currency,
                'email' => $best2pay_email,
                'phone' => $best2pay_phone,
                'description' => $best2pay_description,
                'signature' => $best2pay_signature,
            ], JSON_THROW_ON_ERROR));
            $best2pay_response = curl_exec($ch);
            curl_close($ch);
            $best2pay_response_xml = simplexml_load_string($best2pay_response);
            $best2pay_response_xml_name = $best2pay_response_xml->getName();
            if ($best2pay_response_xml_name === 'error') {
                return array('error' => $best2pay_response_xml->description);
            }
            $delivery_id = (int)simplexml_load_string($best2pay_response)->id;
            if ($delivery_id === 0) {
                return array('error' => 'Регистрация оплаты прошла неудачно');
            }
            $this->orders->update_order($order_id, array('delivery_id' => $delivery_id));
        } catch (Exception $e) {
            return array('error' => 1);
        }

        $best2pay_endpoint = $this->config->best2pay_endpoint;
        $action = "PurchaseBySectorCard";
        $request_url = $best2pay_endpoint . $action;

        $best2pay_description = 'Отправка денег по заявке ' . $order_id;

        $best2pay_signature = base64_encode(md5($best2pay_sector . $delivery_id . $best2pay_password));

        try {
            $ch = curl_init($request_url);
            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'sector' => $best2pay_sector,
                'id' => $delivery_id,
                'description' => $best2pay_description,
                'signature' => $best2pay_signature,
            ], JSON_THROW_ON_ERROR));
            $best2pay_response = curl_exec($ch);
            curl_close($ch);
            $best2pay_response_xml = simplexml_load_string($best2pay_response);
            $best2pay_response_xml_name = $best2pay_response_xml->getName();
            if ($best2pay_response_xml_name === 'error') {
                return array('error' => $best2pay_response_xml->description);
            }
            if ($best2pay_response_xml->state->__toString() !== 'APPROVED') {
                return array('error' => 'Платёж не прошел');
            }
            $this->orders->update_order($order_id, array('status' => 5));

            $ticket =
                [
                    'creator' => 0,
                    'client_lastname' => $order->lastname,
                    'client_firstname' => $order->firstname,
                    'client_patronymic' => $order->patronymic,
                    'head' => 'Займ выдан',
                    'text' => 'Ознакомьтесь с документами по займу',
                    'company_id' => $order['company_id'],
                    'group_id' => $order['group_id'],
                    'order_id' => $order_id,
                    'status' => 0
                ];

            $this->Tickets->add_ticket($ticket);

            return array('success' => 1);
        } catch (Exception $e) {
            return array('error' => 1);
        }

















        $sector = $this->sectors['PAY_CREDIT'];
        $password = $this->passwords[$sector];
                        
        if (!($order = $this->orders->get_order($order_id))) {
            return false;
        }
        
        if ($order->status != 4) {
            return false;
        }
        
        $this->contracts->update_contract($contract->id, array('status' => 9));
        
        
        $data = array(
            'sector' => $sector,
            'amount' => $contract->amount * 100,
            'currency' => $this->currency_code,
//            'pan' => $card->pan,
            'reference' => $contract->id,
            'token' => $card->token,
        );
        $data['signature'] = $this->get_signature(array(
            $data['sector'],
            $data['amount'],
            $data['currency'],
//            $data['pan'],
            $data['token'],
            $password
        ));
        
        $p2pcredit = array(
            'contract_id' => $contract->id,
            'user_id' => $user->id,
            'date' => date('Y-m-d H:i:s'),
            'body' => $data,
            'sector' => $sector,
        );
        if ($p2pcredit_id = $this->add_p2pcredit($p2pcredit)) 
        {
            $response = $this->send('P2PCredit', $data, 'gateweb');
            
            $xml = simplexml_load_string($response);
            $status = (string)$xml->order_state;
            
            $this->update_p2pcredit($p2pcredit_id, array(
                'response' => $response,
                'status' => $status,
                'register_id' => (string)$xml->order_id,
                'operation_id' => (string)$xml->id,
                'complete_date' => date('Y-m-d H:i:s'),
            ));
    
    //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(htmlspecialchars($response));echo '</pre><hr />';
    
            return $status;
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
echo 'SEND';
        $url = $this->url.$type.'/'.$method;
        $string_data = http_build_query($data);
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $string_data);

        $resp = curl_exec($curl);
        curl_close($curl);
        
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
