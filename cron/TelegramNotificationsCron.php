<?php

use Telegram\Bot\Api;

error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');
chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class TelegramNotificationsCron extends Core
{
    protected $token = '5476378779:AAHQmPoqbPB0TW5S8zyo0Ey1abCLZ9hDGq8';
    protected $telegram;


    public function __construct()
    {
        parent::__construct();
        $this->telegram = new Api($this->token);
        $this->run();
    }

    private function run()
    {
        $is_comlited = 0;
        $crons = $this->NotificationsCron->gets($is_comlited);

        foreach ($crons as $cron) {
            $ticket = $this->tickets->get_ticket($cron->ticket_id);

            if (in_array($ticket->theme_id, [8, 17, 20, 22, 24, 31])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'employer']);
            }

            if (in_array($ticket->theme_id, [11, 13, 18, 23, 25, 26, 27, 28, 29, 30, 32, 33, 34, 35, 38])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => ['underwriter', 'middle']]);
            }

            if (in_array($ticket->theme_id, [12, 37])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'middle']);
            }

            if (in_array($ticket->theme_id, [27, 28, 29, 30, 32, 33])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'admin']);
            }

            foreach ($managers as $manager) {
                if($manager->telegram_note == 1){
                    $is_manager = 1;
                    $telegram_check = $this->TelegramUsers->get($manager->id, $is_manager);

                    if(!empty($telegram_check)){
                        $this->telegram->sendMessage([ 'chat_id' => $telegram_check->chat_id, 'text' => $ticket->text]);
                    }
                }
            }

            $this->NotificationsCron->update($cron->id, ['is_complited' => 1]);
        }
    }
}

new SmsNotificationsCron();