<?php

error_reporting(-1);
ini_set('display_errors', 'On');

date_default_timezone_set('Europe/Moscow');

chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class SendPaymentCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->sendPayment();
    }

    private function sendPayment()
    {
        $crons = SendPaymentCronORM::where('is_sent', 0)->where('is_loan_sent', 1)->limit(5)->get();

        foreach ($crons as $cron) {

            $order    = OrdersORM::find($cron->order_id);
            $contract = ContractsORM::find($cron->contract_id);
            $user     = UsersORM::find($cron->user_id);
            $requisites = RequisitesORM::find($cron->requisites_id);
            $dealDate = date('d.m.Y', strtotime($contract->deal_date));

            $fio = "$user->lastname $user->firstname $user->patronymic";

            $description = "Выдача средств по договору микрозайма № $contract->number от $dealDate
            // заемщик $fio ИНН $user->inn. Без налога (НДС)";

            $payment = new stdClass();
            $payment->order_id = $cron->transaction_id;
            $payment->date = date('Y-m-d H:i:s', strtotime($contract->issuance_date));
            $payment->amount = $order->amount;
            $payment->recepient = 9725055162;
            $payment->user_id = (string)$order->user->id;
            $payment->number = '40701810300000000347';
            $payment->description = $description;
            $payment->user_acc_number = $requisites->number;
            $payment->user_bik = $requisites->bik;
            $payment->users_inn = $user->inn;

            $result = $this->soap1c->send_payment($payment);

            if (isset($result->return) && $result->return == 'OK')
                SendPaymentCronORM::where('id', $cron->id)->update(['is_sent' => 1, 'is_error' => 0, 'resp' => 'OK']);
            else
                SendPaymentCronORM::where('id', $cron->id)->update(['is_sent' => 1, 'is_error' => 1, 'resp' => json_encode($result, JSON_UNESCAPED_UNICODE)]);
        }
    }
}
new SendPaymentCron();
