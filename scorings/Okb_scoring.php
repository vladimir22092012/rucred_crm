<?php

class Okb_scoring extends Core
{
    private $user_id;
    private $order_id;
    private $audit_id;
    private $type;

    private $url = "http://51.250.106.75/user-data";


    public function run_scoring($scoring_id)
    {
        $update = array();

        $scoring_type = $this->scorings->get_type('okb');

        if ($scoring = $this->scorings->get_scoring($scoring_id)) {
            if ($order = $this->orders->get_order((int)$scoring->order_id)) {
                if (!empty($order->company_id))
                    $company = $this->companies->get_company(2);

                $data = array(
                    'firstname' => $order->firstname,
                    'patronymic' => $order->patronymic,
                    'lastname' => $order->lastname,
                    'gender' => $order->gender,
                    'birth' => $order->birth,
                    'companyName' => empty($company) ? 'не указана' : $company->name,
                    'passport_serial' => str_replace([' ', '-'], '', $order->passport_serial),
                    'passport_date' => $order->passport_date,
                    'xml_string' => 1,
                    'request_code' => 40,
                );
                if ($this->settings->okb_mode == 'test')
                    $data['is_test'] = 1;

                $xml_resp = $this->load($data);
                
                if (!empty($xml_resp->data))
                {
                    $expl = explode('<s>', $xml_resp->data);
                    array_shift($expl);
                    $xml_resp_data = '<s>'.implode('<s>', $expl);
                    $expl = explode('</s>', $xml_resp_data);
                    array_pop($expl);
                    $xml_resp_data = implode('</s>', $expl).'</s>';
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($xml_resp_data, $xml_resp->data);echo '</pre><hr />';
                }
                
                $data = array(
                    'firstname' => $order->firstname,
                    'patronymic' => $order->patronymic,
                    'lastname' => $order->lastname,
                    'gender' => $order->gender,
                    'birth' => $order->birth,
                    'companyName' => empty($company) ? 'не указана' : $company->name,
                    'passport_serial' => str_replace([' ', '-'], '', $order->passport_serial),
                    'passport_date' => $order->passport_date,
                    'xml_string' => 0,
                    'request_code' => 40,
                );
                if ($this->settings->okb_mode == 'test')
                    $data['is_test'] = 1;

                $resp = $this->load($data);
                
                $resp->xml = $xml_resp_data;

                if (empty($resp->message)) {

                    $filtering = array_filter((array)$resp, function($var){
                        return $var->value;
                    });
                    if (empty($filtering))
                    {
                        $update = array(
                            'status' => 'completed',
                            'body' => serialize($resp),
                            'success' => 0,
                            'string_result' => 'User report not found',
                        );
                        
                    }
                    else
                    {
                        $update = array(
                            'status' => 'completed',
                            'body' => serialize($resp),
                            'success' => 1,
                            'string_result' => 'Проверка пройдена',
                        );
                        
                    }

                } else {
                    $update = array(
                        'status' => 'error',
                        'body' => serialize($resp),
                        'success' => 0,
                        'string_result' => $resp->message,
                    );
                }

            } else {
                $update = array(
                    'status' => 'error',
                    'string_result' => 'не найдена заявка'
                );
            }

            if (!empty($update))
                $this->scorings->update_scoring($scoring_id, $update);

            return $update;
        }
    }


    public function load($data)
    {
        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $resp = curl_exec($ch);
        curl_close($ch);

        $this->soap1c->logging(__METHOD__, $this->url, $data, json_decode($resp), 'okb.txt');

        return json_decode($resp);
    }

}