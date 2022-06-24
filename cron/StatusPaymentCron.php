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
            $this->Soap1c->StatusPaymentOrder($order->order_id);
        }
    }
}

new StatusPaymentCron();