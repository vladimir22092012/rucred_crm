<?php
error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');


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
                $payment_schedule = $this->PaymentsSchedules->get(['order_id' => $contract->order_id, 'actual' => 1]);
                $payment_schedule = json_decode($payment_schedule->schedule, true);
                $now = date('Y-m-d');
                $end_period = '';

                uksort(
                    $payment_schedule,
                    function ($a, $b) {

                        if ($a == $b) {
                            return 0;
                        }

                        return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
                    });

                foreach ($payment_schedule as $payday => $payment) {
                    if ($payday != 'result') {
                        $payday = date('Y-m-d', strtotime($payday));
                        if ($payday > $now) {
                            $percent = $payment['loan_percents_pay'];
                            $end_period = $payday;

                            if(!isset($start_period))
                                $start_period = date('Y-m-d 00:00:00', strtotime($contract->issuance_date));

                            break;
                        }

                        $start_period = $payday;
                    }
                }

                $start_period = date('Y-m-d 00:00:00', strtotime($start_period));
                $end_period = date('Y-m-d 23:59:59', strtotime($end_period));

                $query = $this->db->placehold("
                SELECT SUM(amount) as sum_amount
                FROM s_operations
                WHERE order_id = ?
                AND `type` = 'PERCENTS'
                AND created between ? and ?
                ", $contract->order_id, (string)$start_period, (string)$end_period);

                $this->db->query($query);

                $all_sum_percents = $this->db->result('sum_amount');

                var_dump($all_sum_percents);



                $start_period = new DateTime($start_period);
                $end_period = new DateTime($end_period);
                $period = date_diff($start_period, $end_period)->days;
                $now_day = date('d');

                $percents_summ = round(($percent * ($now_day/$period)) - $all_sum_percents, 2);

                var_dump($contract->id);
                var_dump($percents_summ);

                /*
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
                    ));
                }
                */
            }
        }
    }
}

new Percents();