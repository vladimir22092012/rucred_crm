<?php

require __DIR__ . '/../vendor/autoload.php';

class StatusPaymentCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->check_status();
    }

    private function check_status()
    {
        $orders = $this->orders->get_orders(['status' => 4, 'settlement_id' => 3]);

        foreach ($orders as $order){

            $this->db->query("
            SELECT *
            FROM s_transactions
            WHERE order_id = $order->order_id
            AND reference = 'issuance'
            AND reason_code = 0
            ORDER BY id DESC
            LIMIT 1
            ");

            $transaction = $this->db->result();

            if(!empty($transaction)){
                $res = $this->Soap1c->StatusPaymentOrder($transaction->id);

                if(isset($res->return) && $res->return == 'Оплачено'){
                    $this->transactions->update_transaction($transaction->id, ['reason_code' => 1]);
                    $this->orders->update_order($order->order_id, ['status' => 5]);

                    $this->operations->add_operation(array(
                        'contract_id' => $order->contract_id,
                        'type' => 'P2P',
                        'transaction_id' => $transaction->id,
                        'user_id' => $order->user_id,
                        'order_id' => $order->order_id,
                        'amount' => $order->amount,
                        'created' => date('Y-m-d H:i:s')
                    ));
                }
            }
        }
    }
}

new StatusPaymentCron();