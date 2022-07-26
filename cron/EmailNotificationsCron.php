<?php
error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');


chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

/**
 * Payment1cCron
 *
 * крон для очереди оповещения по email
 */

class EmailNotificationsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $is_comlited = 0;
        $crons = $this->NotificationsCron->gets($is_comlited);

        foreach ($crons as $cron){
            $ticket = $this->tickets->get_ticket($cron->ticket_id);
        }
    }
}

new EmailNotificationsCron();