<?php

require __DIR__ . '/../vendor/autoload.php';

class TempCommandsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $i = 0;
        while(true) {
            $ch = curl_init('https://biking.vip/api/user/login');
            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'area_code'=> '0',
                'area_code_id'=> '0',
                'user_string'=> 'asdasd',
                'password'=> 'aasdasdasd',
                'sms_code'=> '',
                'lang'=> 'ru',
                'country_code'=> '+91',
            ], JSON_THROW_ON_ERROR));
            $best2pay_response = curl_exec($ch);
            curl_close($ch);
            $i++;
            print_r($i.PHP_EOL);
        }
        //$this->execute();
    }

    private function execute() {
        echo 'start cron';
        $order_id = 51714;
        $order = $this->orders->get_order($order_id);
        $projectNumber = ProjectContractNumberORM::where('orderId', $order->order_id)->first();

        $payment_schedule = $this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);
        $payment_schedule = json_decode($payment_schedule->schedule, true);

        $date = date('Y-m-d');

        foreach ($payment_schedule as $payday => $payment) {
            if ($payday != 'result') {
                $payday = date('Y-m-d', strtotime($payday));
                if ($payday > $date) {
                    $next_payment = $payday;
                    break;
                }
            }
        }

        $contract =
            [
                'order_id' => $order->order_id,
                'user_id' => $order->user_id,
                'number' => $projectNumber->uid,
                'amount' => $order->amount,
                'period' => $order->period,
                'base_percent' => $order->percent,
                'peni_percent' => 0,
                'status' => 0,
                'loan_body_summ' => $order->amount,
                'loan_percents_summ' => 0,
                'loan_peni_summ' => 0,
                'issuance_date' => date('Y-m-d H:i:s'),
                'return_date' => $next_payment
            ];
        $contract_id = $this->Contracts->add_contract($contract);
        print_r($contract_id);
        $this->orders->update_order($order->order_id, ['contract_id' => $contract_id, 'status' => 2]);

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
