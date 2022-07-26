<?php
ini_set("soap.wsdl_cache_enabled", 0);
ini_set('default_socket_timeout', '300');

class Soap1c extends Core
{
    private $log = 1;
    private $log_dir = 'logs/';
    private $link;
    private $login;
    private $password;

    public function __construct()
    {
        parent::__construct();
        $this->log_dir = $this->config->root_dir . $this->log_dir;
        $onec_mode = $this->settings->onec_mode;

        if($onec_mode == 'test')
        {
            $this->link = "http://141.101.178.136:63025/RKO-Test/ws/";
            $this->login = '';
            $this->password = '';
        }
        else
        {
            $this->link = "http://141.101.178.136:63025/RKO/ws/";
            $this->login = 'Администратор';
            $this->password = '';
        }
    }


    /**
     * Soap1c::send_loan()
     *
     * Метод отсылает в 1с данные по заявке
     *
     * @param integer $order_id
     * @return string - в случае успеха должно вернуться ОК
     */
    public function send_loan($order_id)
    {
        if ($order = $this->orders->get_order($order_id)) {

            $contract = $this->contracts->get_contract($order->contract_id);
            
            $company = $this->companies->get_company(2);
            $order->regaddress = $this->addresses->get_address($order->regaddress_id);
            $order->faktaddress = $this->addresses->get_address($order->faktaddress_id);

            $passport_serial = str_replace([' ', '-'], '', $order->passport_serial);
            $passport_series = substr($passport_serial, 0, 4);
            $passport_number = substr($passport_serial, 4, 6);

            $item = new StdClass();

            $item->ID = empty($contract) ? $order->uid : $contract->number;
            $item->Дата = date('YmdHis', strtotime($order->date));
            $item->Срок = $order->period;
            $item->Периодичность = 'День';
            $item->ПроцентнаяСтавка = $order->percent;
            $item->ИдентификаторФормыВыдачи = 'Безналичная';
            $item->ИдентификаторФормыОплаты = 'ТретьеЛицо';
            $item->Сумма = $order->amount;
            $item->Порог = '1.5';
            $item->ИННОрганизации = isset($company) ? $company->inn : '';
            $item->СпособПодачиЗаявления = 'Прямой';
            $item->ПДН = $order->pdn;

            $order->payment_schedule = $this->PaymentsSchedules->get(['order_id'=>$order->order_id, 'actual'=>1]);
            $payment_schedules = array();
            $item->ПСК = $order->payment_schedule->psk;
            if ($order_payment_schedule = (array)json_decode($order->payment_schedule->schedule)) {
                foreach ($order_payment_schedule as $key_date => $payment_schedule) {
                    if ($key_date != 'result') {
                        $payment_schedule_item = new StdClass();

                        $payment_schedule_item->Дата = date('YmdHis', strtotime($key_date));
                        $payment_schedule_item->СуммаОД = $payment_schedule->loan_body_pay;
                        $payment_schedule_item->СуммаПроцентов = $payment_schedule->loan_percents_pay;

                        $payment_schedules[] = $payment_schedule_item;
                    }
                }
                $item->ГрафикПлатежей = $payment_schedules;
            }

            $client = new StdClass();
            $client->id = $order->user_id;
            $client->ФИО = $order->lastname . ' ' . $order->firstname . ' ' . $order->patronymic;
            $client->Фамилия = $order->lastname;
            $client->Имя = $order->firstname;
            $client->Отчество = $order->patronymic;
            $client->ДатаРождения = date('Ymd000000', strtotime($order->birth));
            $client->МестоРождения = $order->birth_place;
            $client->АдресРегистрации = $order->regaddress->adressfull;
            $client->АдресПроживания = $order->faktaddress->adressfull;
            $client->Телефон = $this->format_phone($order->phone_mobile);
            $client->ОКАТО = $order->regaddress->okato;
            $client->ОКТМО = $order->regaddress->oktmo;

            $passport = new StdClass();
            $passport->Серия = $passport_series;
            $passport->Номер = $passport_number;
            $passport->КемВыдан = $order->passport_issued;
            $passport->КодПодразделения = $order->subdivision_code;
            $passport->ДатаВыдачи = $order->passport_date;

            $client->Паспорт = $passport;

            $item->Клиент = $client;

            $request = new StdClass();
            $request->TextJSON = json_encode($item);
            $result = $this->send_request('CRM_WebService', 'Loans', $request, 1, 'exchange.log');

            return $result;
        }
    }


    /**
     * Soap1c::send_payment()
     * Отсылает платежку в 1с
     */
    public function send_payment($payment)
    {
        $item = new StdClass();
        $item->OrderID = rand(1, 9999999999999);
        $item->Дата = date('YmdHis', strtotime($payment->date));
        $item->Сумма = $payment->amount;
        $item->ИННПлательщика = $payment->recepient;
        $item->Клиент_id = $payment->user_id;
        $item->СчетОрганизации = $payment->number;
        $item->НазначениеПлатежа = $payment->description;
        $item->СчетКонтрагента = $payment->user_acc_number;
        $item->БИКБанкаКонтрагента = $payment->user_bik;
        $item->ИННПолучателя = '9725055162';

        var_dump($item);

        $request = new StdClass();
        $request->TextJSON = json_encode($item);
        $result = $this->send_request('CRM_WebService', 'PaymentOrder', $request);

        return $result;
    }

    /**
     * Soap1c::StatusPaymentOrder()
     * Получение статуса платежкки
     */
    public function StatusPaymentOrder($order_id)
    {
        $item = new StdClass();
        $item->OrderID = $order_id;

        $result = $this->send_request('CRM_WebService', 'StatusPaymentOrder', $item);

        return $result;
    }

    /**
     * Soap1c::PaymentArray()
     * Получение оплат
     */

    public function getPayments($date)
    {
        $item = new StdClass();
        $item->Date = $date;

        $result = $this->send_request('CRM_WebService', 'PaymentArray', $item);

        return empty($result->return) ? [] : json_decode($result->return);
    }

    /**
     * Soap1c::format_phone()
     * Форматирует номер телефона в формат принимаемый 1с
     * формат 8(ххх)ххх-хх-хх
     *
     * @param string $phone
     * @return string $format_phone
     */
    public function format_phone($phone)
    {
        if (empty($phone)) {
            return '';
        }

        if ($phone == 'не указан' || $phone == 'не указана') {
            return '';
        }

        $replace_params = array('(', ')', ' ', '-', '+');
        $clear_phone = str_replace($replace_params, '', $phone);

        $substr_phone = mb_substr($clear_phone, -10, 10, 'utf8');
        $format_phone = '7(' . mb_substr($substr_phone, 0, 3, 'utf8') . ')' . mb_substr($substr_phone, 3, 3, 'utf8') . '-' . mb_substr($substr_phone, 6, 2, 'utf8') . '-' . mb_substr($substr_phone, 8, 2, 'utf8');

        return $format_phone;
    }

    private function send_request($service, $method, $request, $log = 1, $logfile = 'soap.log')
    {
        $params = array();
        if (!empty($this->login) || !empty($this->password))
        {
            $params['login'] = $this->login;
            $params['password'] = $this->password;
        }
        
        try {
            $service_url = $this->link . $service . ".1cws?wsdl";

            $client = new SoapClient($service_url, $params);
            $response = $client->__soapCall($method, array($request));
        } catch (Exception $fault) {
            $response = $fault;
        }

        if (!empty($log)) {
            $this->logging(__METHOD__, $service_url . ' ' . $method, (array)$request, (array)$response, $logfile);
        }

        return $response;
    }

    public function logging($local_method, $service, $request, $response, $filename = 'soap.log')
    {
        $log_filename = $this->log_dir . $filename;

        if (date('d', filemtime($log_filename)) != date('d')) {
            $archive_filename = $this->log_dir . 'archive/' . date('ymd', filemtime($log_filename)) . '.' . $filename;
            rename($log_filename, $archive_filename);
            file_put_contents($log_filename, "\xEF\xBB\xBF");
        }

        if (isset($request['TextJSON'])) {
            $request['TextJSON'] = json_decode($request['TextJSON']);
        }
        if (isset($request['ArrayContracts'])) {
            $request['ArrayContracts'] = json_decode($request['ArrayContracts']);
        }
        if (isset($request['ArrayOplata'])) {
            $request['ArrayOplata'] = json_decode($request['ArrayOplata']);
        }

        $str = PHP_EOL . '===================================================================' . PHP_EOL;
        $str .= date('d.m.Y H:i:s') . PHP_EOL;
        $str .= $service . PHP_EOL;
        $str .= var_export($request, true) . PHP_EOL;
        $str .= var_export($response, true) . PHP_EOL;
        $str .= 'END' . PHP_EOL;

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($str);echo '</pre><hr />';

        file_put_contents($this->log_dir . $filename, $str, FILE_APPEND);
    }
}