<?php
error_reporting(-1);
ini_set('display_errors', 'On');

date_default_timezone_set('Europe/Moscow');

chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class ExchangeCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->send_loans();
    }


    /**
     * ExchangeCron::send_loans()
     * Отправляем выданные займы
     * @return void
     */
    private function send_loans()
    {
        $crons = ExchangeCronORM::where('is_sent', 0)->limit(5)->get();

        foreach ($crons as $cron) {

            $canSendYaDiskOrder = OrdersORM::select('id', 'canSendOnec')->where('id', $cron->orderId)->first();
            $canSendYaDiskUser = UsersORM::select('id', 'canSendOnec')->where('id', $cron->userId)->first();

            if ($canSendYaDiskUser->canSendOnec == 0 || $canSendYaDiskOrder->canSendOnec == 0) {
                ExchangeCronORM::where('id', $cron->id)->update(['is_sent' => 1, 'is_error' => 1, 'resp' => 'Запрет отправки']);
                continue;
            }

            $result = $this->soap1c->send_loan($cron->orderId);

            if (isset($result->return) && $result->return == 'OK') {
                ExchangeCronORM::where('id', $cron->id)->update(['is_sent' => 1, 'is_error' => 0, 'resp' => 'OK']);
                SendPaymentCronORM::where('contract_id', $cron->contractId)->update(['is_loan_sent' => 1]);
            } else
                ExchangeCronORM::where('id', $cron->id)->update(['is_sent' => 1, 'is_error' => 1, 'resp' => json_encode($result, JSON_UNESCAPED_UNICODE)]);
        }
    }

}

new ExchangeCron();