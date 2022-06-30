<?php

class Employer_scoring extends Core
{
    
    public function run_scoring($scoring_id)
    {
        $update = array();
        
    	$scoring_type = $this->scorings->get_type('employer');
        
        if ($scoring = $this->scorings->get_scoring($scoring_id))
        {
            if ($order = $this->orders->get_order((int)$scoring->order_id))
            {
                $fio = "$order->lastname $order->firstname $order->patronymic";

                $payment_attestation = $this->PaymentsAttestation->get($fio);

                if(empty($payment_attestation)){
                    $response = 'Нет сведений по данному клиенту';

                    $update = array(
                        'status' => 'completed',
                        'body' => $response,
                        'success' => 0,
                        'string_result' => $response
                    );
                }else{
                    $response = "за 02.22 сумма $payment_attestation";

                    $update = array(
                        'status' => 'completed',
                        'body' => $response,
                        'success' => 1,
                        'string_result' => $response
                    );
                }

                $update['string_result'] = $response;
                
                $this->scorings->update_scoring($scoring_id, $update);

                
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
    
}