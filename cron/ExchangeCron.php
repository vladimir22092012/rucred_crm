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
            $result = $this->soap1c->send_loan($cron->order_id);

            if (isset($result->return) && $result->return == 'OK')
                ExchangeCronORM::where('id', $cron->id)->update(['is_sent' => 1, 'resp' => 'OK']);
            else
                ExchangeCronORM::where('id', $cron->id)->update(['is_sent' => 1, 'is_error' => 1, 'resp' => $result]);
        }
    }

}

new ExchangeCron();