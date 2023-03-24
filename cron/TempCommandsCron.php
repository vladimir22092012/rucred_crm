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
        echo 'start cron';
        //$this->createContracts();
        $this->checkPhotos();
    }

    private function checkPhotos() {
        $users = UsersORM::all();
        foreach ($users as $user) {
            $files = FilesORM::query()->where('user_id', '=', $user->id)->first();
            if (!$files) {
                $dir = __DIR__.'/../files/users/'.$user->id;
                if (file_exists($dir)) {
                    $dir_files = scandir($dir);
                    if (is_array($dir_files) && count($dir_files) > 2) {
                        echo $user->id.',';
                    }
                }
            }
        }
    }

    private function createContracts() {
        $orders = OrdersORM::all();
        echo count($orders).PHP_EOL;
        foreach ($orders as $order) {
            $number = ProjectContractNumberORM::query()->where('orderId', '=', $order->id)->first();
            if (!$number) {
                $contract = ContractsORM::query()->where('order_id', '=', $order->id)->first();
                if ($contract) {
                    ProjectContractNumberORM::create([
                        'orderId' => $order->id,
                        'userId' => $order->user_id,
                        'uid' => $contract->number,
                        'created' => $order->date,
                        'updated' => $order->date,
                    ]);
                    echo "Order";
                }
            }
        }
    }
}

new TempCommandsCron();
