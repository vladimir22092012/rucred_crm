<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class ArchiveOrdersCron extends Core
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    /**
     * @throws Exception
     */
    private function run()
    {
        $orders = $this->Orders->get_orders(['status' => [0,1,2,4,8,11,13,14,15,20]]);

        foreach ($orders as $order) {
            $orderCreationDate = new DateTime(date('Y-m-d H:i:s', strtotime($order->date)));

            if ($orderCreationDate->diff(now())->days >= 3) {
                $this->Orders->update_order($order->order_id, ['is_archived' => true]);
            }
        }
    }
}

new ArchiveOrdersCron();
