<?php

chdir('..');

require 'autoload.php';

class PayService extends Core
{
    private $response = array();
    
    private $password = 'AX6878EK';
    
    public function __construct()
    {
    	$this->run();
    }
    
    private function run()
    {
        $password = $this->request->get('password');
        if ($password != $this->password)
            $this->show_error('Укажите пароль обмена');
        
        if ($operation_id = $this->request->get('operation_id'))
        {
            if ($register_id = $this->request->get('register_id'))
            {
                if ($transaction = $this->transactions->get_operation_transaction($register_id, $operation_id))
                {
                    if ($operation = $this->operations->get_transaction_operation($transaction->id))
                    {
                        $this->send_operation($operation);
                    }
                    else
                    {
                        $this->show_error('Операция не найдена');
                    }
                }
                else
                {
                    $this->show_error('Не найдена транзакция');
                }
            }
            else
            {
                $this->show_error('Не указан параметр register_id');
            }
        }
        else
        {
            $this->show_error('Не указан параметр operation_id');
        }

        
        $this->output();
    }
    
    private function send_operation($operation)
    {
        $operation->contract = $this->contracts->get_contract($operation->contract_id);
        $operation->transaction = $this->transactions->get_transaction($operation->transaction_id);
        if ($operation->transaction->insurance_id)
            $operation->transaction->insurance = $this->insurances->get_insurance($operation->transaction->insurance_id);

        if ($operation->type == 'REJECT_REASON')
        {
            $result = $this->soap1c->send_reject_reason($operation);
            if (!((isset($result->return) && $result->return == 'OK') || $result == 'OK'))
            {
                $order = $this->orders->get_order($operation->order_id);
                $this->soap1c->send_order($order);
                $result = $this->soap1c->send_reject_reason($operation);
            }
        }
        else
        {
            $result = $this->soap1c->send_payments(array($operation));
        }
        
        if ((isset($result->return) && $result->return == 'OK') || $result == 'OK')
        {
            $this->operations->update_operation($operation->id, array(
                'sent_date' => date('Y-m-d H:i:s'),
                'sent_status' => 2
            ));
            
            $this->response['success'] = 1;
            $this->output();
        }
        else
        {
            $this->show_error('Ошибка при отправке');
        }
        
    
    }
    
    private function show_error($message)
    {
        $this->response['error'] = 1;
        $this->response['message'] = $message;
        
        $this->output();            
        
    }
    
    private function output()
    {
        header('Content-type:application/json');
        echo json_encode($this->response);
        
        exit;
    }
}
new PayService();