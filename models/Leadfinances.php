<?php

class Leadfinances extends Core
{
    private $log_dir  = 'logs/';

    public function send_lead_to_leadfinances($order)
    {
        $token = '97d5488adca24070b182b441e4295dc3';

        $phone = $order->phone_mobile;
        $name = $order->firstname;
        $lastName = $order->lastname;
        $patronymic = $order->patronymic;
        $amount = $order->amount;
        $period = $order->period;

        $birthday = date("Y-m-d", strtotime($order->birth));

        $data = [
            'token' => $token,
            'phone' => '+'.$phone,
            'type' => 1,
            'policy_accept' => 1,
            'mailings_accept' => 1,
            'first_name' => $name,
            'middle_name' => $patronymic,
            'last_name' => $lastName,
            'amount' => $amount,
            'term' => $period,
        ];

        $data['birthday'] = $birthday;//"Y-m-d"

        if (!empty($order->Faktregion)) {
            $data['region_fact'] = $order->Faktregion;
        }

        if (!empty($order->Faktcity)) {
            $data['city_fact'] = $order->Faktcity;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.gate.leadfinances.com/v1/lead/add',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data)
        ));
        $response = curl_exec($curl);
        
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        $this->logging_(__METHOD__, 'send_lead', (array)$data, json_decode($response), 'leadfinances.txt');
    }

    public function logging_($local_method, $service, $request, $response, $filename)
    {
        $log_filename = $this->log_dir.$filename;
        
        if (date('d', filemtime($log_filename)) != date('d')) {
            $archive_filename = $this->log_dir.'archive/'.date('ymd', filemtime($log_filename)).'.'.$filename;
            rename($log_filename, $archive_filename);
            file_put_contents($log_filename, "\xEF\xBB\xBF");
        }
        
        if (isset($request['TextJson'])) {
            $request['TextJson'] = json_decode($request['TextJson']);
        }
        if (isset($request['ArrayContracts'])) {
            $request['ArrayContracts'] = json_decode($request['ArrayContracts']);
        }
        if (isset($request['ArrayOplata'])) {
            $request['ArrayOplata'] = json_decode($request['ArrayOplata']);
        }
        
        $str = PHP_EOL.'==================================================================='.PHP_EOL;
        $str .= date('d.m.Y H:i:s').PHP_EOL;
        $str .= $service.PHP_EOL;
        $str .= var_export($request, true).PHP_EOL;
        $str .= var_export($response, true).PHP_EOL;
        $str .= 'END'.PHP_EOL;
        
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($str);echo '</pre><hr />';
        
        file_put_contents($this->log_dir.$filename, $str, FILE_APPEND);
    }
}
