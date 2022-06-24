<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require __DIR__ . '/../vendor/autoload.php';

class Percents extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->add_percents();
    }

    private function add_percents()
    {
        $contracts = $this->contracts->get_contracts(array('status' => 2));

        if(!empty($contracts)){
            foreach ($contracts as $contract)
            {
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
    }
}

new Percents();