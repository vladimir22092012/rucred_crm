<?php
error_reporting(-1);
ini_set('display_errors', 'On');


//chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');
chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * IssuanceCron
 * 
 * Скрипт производит начисление процентов, просрочек, пеней
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
    
        $this->run();
    }
    
    private function run()
    {
        $this->contracts->check_expiration_contracts();
        
        $this->contracts->check_collection_contracts();
        
        $this->contracts->check_sold_contracts();
        
        $this->contracts->distribute_contracts();
        
        // выданные займы
        if ($contracts = $this->contracts->get_contracts(array('status' => 2, 'type' => 'base')))
        {
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contracts);echo '</pre><hr />';
            foreach ($contracts as $contract)
            {
                // если займ не просрочен начисляем по обычной ставке
                $percents_summ = round($contract->loan_body_summ / 100 * $contract->base_percent, 2);

                 
                $this->contracts->update_contract($contract->id, array(
                    'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ
                ));
                
                if ($percents_summ > 0)
                {
                    $this->operations->add_operation(array(
                        'contract_id' => $contract->id,
                        'user_id' => $contract->user_id,
                        'order_id' => $contract->order_id,
                        'type' => 'PERCENTS',
                        'amount' => $percents_summ,
                        'created' => date('Y-m-d H:i:s'),
                        'loan_body_summ' => $contract->loan_body_summ,
                        'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                        'loan_charge_summ' => $contract->loan_charge_summ,
                        'loan_peni_summ' => $contract->loan_peni_summ,
                    ));
                }
            }
        }
        
        
        // просроченные
        if ($contracts = $this->contracts->get_contracts(array('status' => 4, 'type' => 'base')))
        {
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contracts);echo '</pre><hr />';
            foreach ($contracts as $contract)
            {
                if (empty($contract->sud))
                {
                    // начисляем по обычной ставке
                    $percents_summ = round($contract->loan_body_summ / 100 * $contract->base_percent, 2);
                     
                    $this->contracts->update_contract($contract->id, array(
                        'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ
                    ));
                    
                    if ($percents_summ > 0)
                    {
                        $this->operations->add_operation(array(
                            'contract_id' => $contract->id,
                            'user_id' => $contract->user_id,
                            'order_id' => $contract->order_id,
                            'type' => 'PERCENTS',
                            'amount' => $percents_summ,
                            'created' => date('Y-m-d H:i:s'),
                            'loan_body_summ' => $contract->loan_body_summ,
                            'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                            'loan_charge_summ' => $contract->loan_charge_summ,
                            'loan_peni_summ' => $contract->loan_peni_summ,
                        ));
                    }
                    
                    $charge_percents_summ = 0;
                    if ($contract->sold)
                    {
    /** убираем начисление ответсвенности, сделать что бы она начислялась по алгоритму:
    
    ответственность идёт только на юка, 
    переход на юка на 15 день после пятой пролонгации 
    или 11 дня просрочки
    или 150% суммы займа
    **/
                        // начисляем ответственность
                        $charge_percents_summ = round($contract->loan_body_summ / 100 * $contract->charge_percent, 2);
                         
                        $this->contracts->update_contract($contract->id, array(
                            'loan_charge_summ' => $contract->loan_charge_summ + $charge_percents_summ
                        ));
                        
                        if ($charge_percents_summ > 0)
                        {
                            $this->operations->add_operation(array(
                                'contract_id' => $contract->id,
                                'user_id' => $contract->user_id,
                                'order_id' => $contract->order_id,
                                'type' => 'CHARGE',
                                'amount' => $charge_percents_summ,
                                'created' => date('Y-m-d H:i:s'),
                                'loan_body_summ' => $contract->loan_body_summ,
                                'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                                'loan_charge_summ' => $contract->loan_charge_summ + $charge_percents_summ,
                                'loan_peni_summ' => $contract->loan_peni_summ,
                            ));
                        }
                    }
                    
                    // начисляем пени
                    $peni_summ = round($contract->loan_body_summ / 100 * ($contract->peni_percent / 365), 2);
                     
                    $this->contracts->update_contract($contract->id, array(
                        'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ
                    ));
                    
                    if ($peni_summ > 0)
                    {
                        $this->operations->add_operation(array(
                            'contract_id' => $contract->id,
                            'user_id' => $contract->user_id,
                            'order_id' => $contract->order_id,
                            'type' => 'PENI',
                            'amount' => $peni_summ,
                            'created' => date('Y-m-d H:i:s'),
                            'loan_body_summ' => $contract->loan_body_summ,
                            'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                            'loan_charge_summ' => $contract->loan_charge_summ + $charge_percents_summ,
                            'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ,
                        ));
                    }
                }
            }
        }
        
    }
    
    
}

$cron = new IssuanceCron();
