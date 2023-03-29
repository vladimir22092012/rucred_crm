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
        //$this->checkPhotos();
        //$this->sendYaDisk();
    }

    private function sendYaDisk() {
        $orders = OrdersORM::query()->where('status', '=', 5)->get();
        print_r(count($orders));
        foreach ($orders as $order) {
            $cron =
                [
                    'order_id' => $order->id,
                    'pak' => 'first_pak'
                ];

            $this->YaDiskCron->add($cron);
            $cron =
                [
                    'order_id' => $order->id,
                    'pak' => 'second_pak'
                ];

            $this->YaDiskCron->add($cron);
        }
    }

    private function checkPhotos() {
        /*FilesORM::query()->whereIn('user_id', [22692,22703,22704,22705,22706,22707,22708,22749,22753,22754,22759,22761,22764,22778,22803,22811,22813,22815,22823,22824,22828,22834,22839,22840,22846,22847,22864,22867,22870,23151,23152,23236,23239,23252,23260])->delete();
        die();*/
        $users = UsersORM::query()->whereIn('id', [22692,22703,22704,22705,22706,22707,22708,22749,22753,22754,22759,22761,22764,22778,22803,22811,22813,22815,22823,22824,22828,22834,22839,22840,22846,22847,22864,22867,22870,23151,23152,23236,23239,23252,23260])->get();
        foreach ($users as $user) {
            $files = FilesORM::query()->where('user_id', '=', $user->id)->first();
            if (!$files) {
                $dir = __DIR__.'/../files/users/'.$user->id;
                if (file_exists($dir)) {
                    $dir_files = scandir($dir);
                    if (is_array($dir_files) && count($dir_files) > 2) {
                        $i = 0;
                        foreach ($dir_files as $dir_file) {
                            if ($dir_file != '.' && $dir_file != '..') {
                                $types = [
                                    'Паспорт: разворот',
                                    'Селфи с паспортом',
                                    'Паспорт: регистрация',
                                ];
                                if (strpos($dir_file, '.jpg') != false || strpos($dir_file, '.png') != false || strpos($dir_file, '.jpeg') != false) {
                                    if ($i > 2) {
                                        $i = 2;
                                    }
                                    FilesORM::create([
                                        'user_id' => $user->id,
                                        'name' => $dir_file,
                                        'type' => $types[$i],
                                        'status' => '2',
                                        'created' => $user->created,
                                        'sent_1c' => '0',
                                        'sent_date' => null,
                                    ]);
                                    echo "User ".$user->id." save files done\n";
                                    $i++;
                                }

                            }
                        }
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
