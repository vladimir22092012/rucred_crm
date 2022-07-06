<?php

class Okb_scoring extends Core
{
    private $user_id;
    private $order_id;
    private $audit_id;
    private $type;
    
    private $url = "http://151.248.125.86/user-data";
    

    
    public function run_scoring($scoring_id)
    {
        $update = array();
        
    	$scoring_type = $this->scorings->get_type('okb');
        
        if ($scoring = $this->scorings->get_scoring($scoring_id))
        {
            if ($order = $this->orders->get_order((int)$scoring->order_id))
            {
                if (!empty($order->company_id))
                    $company = $this->companies->get_company($order->company_id);
                
                $data = array(
                    'firstname' => $order->firstname,
                    'patronymic' => $order->patronymic,
                    'lastname' => $order->lastname,
                    'gender' => $order->gender,
                    'birth' => $order->birth,
                    'companyName' => empty($company) ? 'не указана' : $company->name,
                    'passport_serial' => str_replace([' ','-'], '', $order->passport_serial),
                    'passport_date' => $order->passport_date,
                );
                if ($this->settings->okb_mode == 'test')
                    $data['is_test'] = 1;
                
                $resp = $this->load($data);
                
                if (empty($resp->message))
                {
                    $update = array(
                        'status' => 'completed',
                        'body' => serialize($resp),
                        'success' => 1,
                        'string_result' => 'Проверка пройдена',
                    );
                    
                }
                else
                {
                    $update = array(
                        'status' => 'error',
                        'body' => serialize($resp),
                        'success' => 0,
                        'string_result' => $resp->message,
                    );
                }
                
            }
            else
            {
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
        
        return json_decode($resp);
    }
    
}