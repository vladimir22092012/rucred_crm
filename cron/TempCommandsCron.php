<?php

require __DIR__ . '/../vendor/autoload.php';

class TempCommandsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->execute();
    }

    private function execute() {
        $orders = $this->orders->get_orders(['status' => 14]);
        foreach ($orders as $order) {
            $exist = DocumentsORM::query()
                ->where('order_id', '=', $order->order_id)
                ->where('type', '=', 'IDENTIFICATION')
                ->first();
            if (!$exist) {

                $asp_log =
                    [
                        'user_id' => $order->user_id,
                        'order_id' => $order->order_id,
                        'created' => date('Y-m-d H:i:s'),
                        'type' => 'employ_sms',
                        'recepient' => $order->phone_mobile,
                        'manager_id' => 33
                    ];

                $asp_id = $this->AspCodes->add_code($asp_log);

                $this->documents->create_document(array(
                    'user_id' => $order->user_id,
                    'order_id' => $order->order_id,
                    'type' => 'IDENTIFICATION',
                    'params' => $order,
                    'numeration' => '03.07',
                    'asp_id' => $asp_id,
                    'hash' => sha1(rand(1, 99999999999)),
                    'stage_type' => 'reg-docs'
                ));
            }
        }
    }
}

new TempCommandsCron();
