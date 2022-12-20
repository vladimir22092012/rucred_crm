<?php

require __DIR__ . '/../vendor/autoload.php';

class UnreabilityOrderCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->changeStatus();
    }

    private function changeStatus()
    {
        $orders = OrdersORM::where('status', 12)
            ->where('unreability', 0)
            ->get();

        if (!empty($orders)) {
            foreach ($orders as $order) {
                $now = new DateTime(date('Y-m-d'));
                $createDate = new DateTime(date('Y-m-d', strtotime($order->date)));

                if (date_diff($now, $createDate)->days > 3) {
                    OrdersORM::where('id', $order->id)->update(['unreability' => 1]);
                    UsersORM::where('id', $order->user_id)->update(['stage_registration' => 8]);
                }
            }
        }
    }
}

new UnreabilityOrderCron();