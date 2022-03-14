<?php
error_reporting(-1);
ini_set('display_errors', 'On');


//chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');
chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * IssuanceCron
 * 
 * Скрипт выдает кредиты, и списывает страховку
 * 
 * @author Ruslan Kopyl
 * @copyright 2021
 * @version $Id$
 * @access public
 */
class IssuanceCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        file_put_contents($this->config->root_dir.'cron/log.txt', date('d-m-Y H:i:s').' Issuance RUN'.PHP_EOL, FILE_APPEND);
        
        $i = 0;
        while ($i < 5)
        {
            $this->run();
            $i++;
        }
    }
    
    private function run()
    {
        if ($contracts = $this->contracts->get_contracts(array('status' => 1, 'limit' => 1)))
        {
            
            foreach ($contracts as $contract)
            {
                $amount = intval($contract->amount * 100);
                
                $res = $this->best2pay->pay_contract($contract->id);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($res, $contract);echo '</pre><hr />';                
                if ($res == 'COMPLETED')
                {
                    $ob_date = new DateTime();
                    $ob_date->add(DateInterval::createFromDateString($contract->period.' days'));
                    $return_date = $ob_date->format('Y-m-d H:i:s');

                    $this->contracts->update_contract($contract->id, array(
                        'status' => 2, 
                        'inssuance_date' => date('Y-m-d H:i:s'),
                        'loan_body_summ' => $contract->amount,
                        'loan_percents_summ' => 0,
                        'return_date' => $return_date,
                    ));
                    
                    $this->orders->update_order($contract->order_id, array('status'=>5));
                    
                    $this->operations->add_operation(array(
                        'contract_id' => $contract->id,
                        'user_id' => $contract->user_id,
                        'order_id' => $contract->order_id,
                        'type' => 'P2P',
                        'amount' => $contract->amount,
                        'created' => date('Y-m-d H:i:s'),
                    ));
                    
                    if ($order = $this->orders->get_order((int)$contract->order_id))
                    {
                        $this->soap1c->send_order_status($order->id_1c, 'Выдан');
                    }

                    
                    //TODO: Индивидуальные условия
                    $this->create_document('IND_USLOVIYA_NL', $contract);
                    $this->create_document('ANKETA_PEP', $contract);

                    // Снимаем страховку если есть
                    if (!empty($contract->service_insurance))
                    {
                        $insurance_summ = round($contract->amount * 0.1535, 2);
                        $insurance_amount = $insurance_summ * 100;
                        
                        $description = 'Страховой полис';
                        
                        $response = $this->best2pay->recurrent($contract->card_id, $insurance_amount, $description);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(htmlspecialchars($response));echo '</pre><hr />';                
                        
                        $xml = simplexml_load_string($response);
                        $status = (string)$xml->state;
                
                        if ($status == 'APPROVED')
                        {
                            $transaction = $this->transactions->get_operation_transaction($xml->order_id, $xml->id);
                            
                            $contract = $this->contracts->get_contract($contract->id);
                            
                            $payment_amount = $insurance_amount / 100;
                            
                            $operation_id = $this->operations->add_operation(array(
                                'contract_id' => $contract->id,
                                'user_id' => $contract->user_id,
                                'order_id' => $contract->order_id,
                                'type' => 'INSURANCE',
                                'amount' => $payment_amount,
                                'created' => date('Y-m-d H:i:s'),
                                'transaction_id' => $transaction->id,
                            ));
                            
                            $close_contracts = $this->contracts->get_contracts(array('user_id' => $contract->user_id, 'status' => 3));
                            
                            $protection = count($close_contracts) == 1;
                            $protection = 0; // убираем кредитную защиту
                            
                            $insurance_id = $this->insurances->add_insurance(array(
                                'amount' => $payment_amount,
                                'user_id' => $contract->user_id,
                                'create_date' => date('Y-m-d H:i:s'),
                                'start_date' => date('Y-m-d 00:00:00', time() + (1 * 86400)),
                                'end_date' => date('Y-m-d 23:59:59', time() + (14 * 86400)),
                                'operation_id' => $operation_id,
                                'protection' => $protection,
                            ));
                            
                            $this->contracts->update_contract($contract->id, array(
                                'insurance_id' => $insurance_id
                            ));
                            
                            $order = $this->orders->get_order((int)$contract->order_id);
                            
                            
                            $contract->insurance_id = $insurance_id;
                            //TODO: Заявление на страхование
                            $this->create_document('DOP_USLUGI_VIDACHA', $contract);
                            /*
                            $this->documents->create_document(array(
                                'user_id' => $contract->user_id,
                                'order_id' => $contract->order_id,
                                'contract_id' => $contract->id,
                                'type' => 'DOP_USLUGI_VIDACHA',
                                'params' => $params,                
                            ));
                            */

                            //TODO: Страховой полиc
                            $this->create_document('POLIS_STRAHOVANIYA', $contract);
                            /*
                            $this->documents->create_document(array(
                                'user_id' => $contract->user_id,
                                'order_id' => $contract->order_id,
                                'contract_id' => $contract->id,
                                'type' => 'POLIS_STRAHOVANIYA',
                                'params' => $params,                
                            ));
                            */

                            
                            //Отправляем чек по страховке
                            $return = $this->cloudkassir->send_insurance($operation_id);
                            
                            if (empty($protection))
                            {
                                $resp = json_decode($return);
                                
                                $this->receipts->add_receipt(array(
                                    'user_id' => $contract->user_id,
                                    'order_id' => $contract->order_id,
                                    'contract_id' => $contract->id,
                                    'insurance_id' => $insurance_id,
                                    'receipt_url' => (string)$resp->Model->ReceiptLocalUrl,
                                    'response' => serialize($return),
                                    'created' => date('Y-m-d H:i:s'),
                                ));
                            }
                            
                            
                            if ($order = $this->orders->get_order((int)$contract->order_id))
                            {
                                if (!empty($order->utm_source) && $order->utm_source == 'leadcraft' && !empty($order->id_1c) && !empty($order->click_hash)) {
                                    try {
                                        $this->leadgens->send_approved_postback($order->click_hash, $order->id_1c);
                                    } catch (\Throwable $th) {
                                        //throw $th;
                                    }
                                }
                            }
                            

                            return true;
                            
                        }
                        else
                        {
                            
                        }
                    }
                }
                else
                {
                    $this->contracts->update_contract($contract->id, array('status' => 6));

                    $this->orders->update_order($contract->order_id, array('status' => 6)); // статус 6 - не удалосб выдать
                
                    if ($order = $this->orders->get_order((int)$contract->order_id))
                    {
                        $this->soap1c->send_order_status($order->id_1c, 'Отказано');
                    }
                }

                
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($res);echo '</pre><hr />';                
            }
        }
    }
    
    public function create_document($document_type, $contract)
    {
        $ob_date = new DateTime();
        $ob_date->add(DateInterval::createFromDateString($contract->period.' days'));
        $return_date = $ob_date->format('Y-m-d H:i:s');

        $return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
        $return_amount_rouble = (int)$return_amount;
        $return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

        $contract_order = $this->orders->get_order((int)$contract->order_id);
        
        $params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'phone' => $contract_order->phone_mobile,
            'birth' => $contract_order->birth,
            'number' => $contract->number,
            'contract_date' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
            'return_date' => $return_date,
            'return_date_day' => date('d', strtotime($return_date)),
            'return_date_month' => date('m', strtotime($return_date)),
            'return_date_year' => date('Y', strtotime($return_date)),
            'return_amount' => $return_amount,
            'return_amount_rouble' => $return_amount_rouble,
            'return_amount_kop' => $return_amount_kop,
            'base_percent' => $contract->base_percent,
            'amount' => $contract->amount,
            'period' => $contract->period,
            'return_amount_percents' => round($contract->amount * $contract->base_percent * $contract->period / 100, 2),
            'passport_serial' => $contract_order->passport_serial,
            'passport_date' => $contract_order->passport_date,
            'subdivision_code' => $contract_order->subdivision_code,
            'passport_issued' => $contract_order->passport_issued,
            'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
            'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
            'asp' => $contract->accept_code,
            'insurance_summ' => round($contract->amount * 0.1535, 2),
        );
        $regaddress_full = empty($contract_order->Regindex) ? '' : $contract_order->Regindex.', ';
        $regaddress_full .= trim($contract_order->Regregion.' '.$contract_order->Regregion_shorttype);
        $regaddress_full .= empty($contract_order->Regcity) ? '' : trim(', '.$contract_order->Regcity.' '.$contract_order->Regcity_shorttype);
        $regaddress_full .= empty($contract_order->Regdistrict) ? '' : trim(', '.$contract_order->Regdistrict.' '.$contract_order->Regdistrict_shorttype);
        $regaddress_full .= empty($contract_order->Reglocality) ? '' : trim(', '.$contract_order->Reglocality.' '.$contract_order->Reglocality_shorttype);
        $regaddress_full .= empty($contract_order->Reghousing) ? '' : ', д.'.$contract_order->Reghousing;
        $regaddress_full .= empty($contract_order->Regbuilding) ? '' : ', стр.'.$contract_order->Regbuilding;
        $regaddress_full .= empty($contract_order->Regroom) ? '' : ', к.'.$contract_order->Regroom;

        $params['regaddress_full'] = $regaddress_full;

        if (!empty($contract->insurance_id))
        {
            $params['insurance'] = $this->insurances->get_insurance($contract->insurance_id);
        }
        

        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => $document_type,
            'params' => $params,                
        ));

    }
    
}

$cron = new IssuanceCron();
