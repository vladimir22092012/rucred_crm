<?php

use App\Services\MailService;

error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');
chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class MailNotificationsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $crons = $this->NotificationsCron->gets();

        foreach ($crons as $cron) {
            $ticket = $this->tickets->get_ticket($cron->ticket_id);
            $order = $this->orders->get_order($ticket->order_id);

            if (in_array($ticket->theme_id, [14, 21, 36, 39])) {
                $users_preferred = $this->UserContactPreferred->gets();

                foreach ($users_preferred as $user){
                    if($order->user_id == $user->user_id && $user->contact_type_id == 2 && !empty($order->email)){
                        $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
                        $mailResponse = $mailService->send(
                            'rucred@ucase.live',
                            $order->email,
                            'RuCred | Уведомление',
                            "$ticket->head",
                            "<h2>$ticket->text</h2>"
                        );
                    }
                }
                $this->NotificationsCron->update($cron->id, ['is_complited' => 1]);
            }
        }
    }
}

new SmsNotificationsCron();