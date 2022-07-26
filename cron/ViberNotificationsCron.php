<?php

use Viber\Bot;
use Viber\Api\Sender;
use Viber\Client;

error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');
chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class ViberNotificationsCron extends Core
{
    protected $apy_key = '4f668e111aa7defb-b74d69004af9235c-371097ebb1cfa25e';

    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $is_comlited = 0;
        $crons = $this->NotificationsCron->gets($is_comlited);

        $botSender = new Sender([
            'name' => 'Whois bot',
            'avatar' => 'https://developers.viber.com/img/favicon.ico',
        ]);

        $bot = new Bot(['token' => $this->apy_key]);

        foreach ($crons as $cron) {
            $ticket = $this->tickets->get_ticket($cron->ticket_id);

            if (in_array($ticket->theme_id, [8, 17, 20, 22, 24, 31])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'employer']);
            }

            if (in_array($ticket->theme_id, [11, 13, 18, 38])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'underwriter']);
            }

            if (in_array($ticket->theme_id, [12, 37])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'middle']);
            }

            if (in_array($ticket->theme_id, [23, 25, 26, 34, 35])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => ['underwriter', 'middle']]);
            }

            if (in_array($ticket->theme_id, [27, 28, 29, 30, 32, 33])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => ['underwriter', 'middle', 'admin']]);
            }

            foreach ($managers as $manager) {
                if($manager->viber_note == 1){
                    $is_manager = 1;
                    $viber_check = $this->ViberUsers->get($manager->id, $is_manager);

                    if(!empty($telegram_check)){
                        $bot->getClient()->sendMessage(
                            (new \Viber\Api\Message\Text())
                                ->setSender($botSender)
                                ->setReceiver($viber_check->chat_id)
                                ->setText($ticket->text)
                        );
                    }
                }
            }

            $this->NotificationsCron->update($cron->id, ['is_complited' => 1]);
        }
    }
}

new SmsNotificationsCron();