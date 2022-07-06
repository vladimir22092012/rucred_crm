<?php
error_reporting(0);
ini_set('display_errors', 'Off');

chdir('..');

require 'autoload.php';

class Best2payAjax extends Ajax
{
    public function run()
    {
        $action = $this->request->get('action', 'string');
        
        switch($action):
            
            case 'get_payment_link':
                $this->get_payment_link();
            break;
            
            case 'callback':
                $this->callback_action();                
            break;
            
            default:
                $this->response['error'] = 'UNDEFINED_ACTION';
            
        endswitch;
        
        $this->output();
    }
    
    private function callback_action()
    {
        $register_id = $this->request->get('id', 'integer');
        $operation = $this->request->get('operation', 'integer');
        $reference = $this->request->get('reference', 'integer');
        $error = $this->request->get('error', 'integer');
        $code = $this->request->get('code', 'integer');

        $sector = $this->best2pay->get_sector('PAYMENT');


        if (!empty($register_id)) 
        {
            if ($transaction = $this->transactions->get_register_id_transaction($register_id)) 
            {
                if ($transaction_operation = $this->operations->get_transaction_operation($transaction->id)) 
                {
                    $meta_title = 'Оплата уже принята';
                    $this->design->assign('error', 'Оплата уже принята.');
                } 
                else 
                {

                    if (empty($operation)) 
                    {
                        $register_info = $this->best2pay->get_register_info($transaction->sector, $register_id);
                        $xml = simplexml_load_string($register_info);

                        foreach ($xml->operations as $xml_operation)
                            if ($xml_operation->operation->state == 'APPROVED')
                                $operation = (string)$xml_operation->operation->id;
                    }


                    if (!empty($operation)) 
                    {
                        $operation_info = $this->best2pay->get_operation_info($transaction->sector, $register_id, $operation);
                        $xml = simplexml_load_string($operation_info);
                        $operation_state = strval($xml->state);
                        
                        if ($operation_state == 'APPROVED') 
                        {
                            $this->transactions->catch_transaction($transaction, $xml);
                        }
                        else 
                        {
                            $reason_code_description = $this->best2pay->get_reason_code_description($code);
                            $this->design->assign('reason_code_description', $reason_code_description);

                            $meta_title = 'Не удалось оплатить';
                            $this->design->assign('error', 'При оплате произошла ошибка.');
                        }
                        $this->transactions->update_transaction($transaction->id, array(
                            'operation' => $operation,
                            'callback_response' => $operation_info,
                            'reason_code' => $reason_code
                        ));


                    } 
                    else 
                    {
                        $callback_response = $this->best2pay->get_register_info($transaction->sector, $register_id, $operation);
                        $this->transactions->update_transaction($transaction->id, array(
                            'operation' => 0,
                            'callback_response' => $callback_response
                        ));
                        //echo __FILE__.' '.__LINE__.'<br /><pre>';echo(htmlspecialchars($callback_response));echo '</pre><hr />';
                        $meta_title = 'Не удалось оплатить';
                        $this->design->assign('error', 'При оплате произошла ошибка. Код ошибки: ' . $error);

                    }
                }
            } 
            else 
            {
                $meta_title = 'Ошибка: Транзакция не найдена';
                $this->design->assign('error', 'Ошибка: Транзакция не найдена');
            }


        } 
        else 
        {
            $meta_title = 'Ошибка запроса';
            $this->design->assign('error', 'Ошибка запроса');
        }


//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($_GET);echo '</pre><hr />';

    }
    
    private function get_payment_link()
    {
        if (!empty($_SESSION['looker_mode']))
            return false;
        
        $amount = (float)str_replace(',', '.', $this->request->get('amount'));
        $contract_id = $this->request->get('contract_id', 'integer');
        $prolongation = $this->request->get('prolongation', 'integer');
        $sms = $this->request->get('code_sms', 'string');
        
        $card_id = $this->request->get('card_id', 'integer');
        
        if (empty($amount))
        {
            $this->response['error'] = 'EMPTY_AMOUNT';
        }
        else
        {
            $amount = $amount * 100;
            $this->response['link'] = $this->best2pay->get_payment_link($amount, $contract_id, $prolongation, $card_id, $sms);
        }
    }
    
}
$ajax = new Best2payAjax();
$ajax->run();