<?php
error_reporting(-1);
ini_set('display_errors', 'On');

date_default_timezone_set('Europe/Moscow');

chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class TicketNotificationCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->check_and_send_notify();
    }


    /**
     * Отправляем уведомления по не тронутому запросу
     * @return void
     */
    private function check_and_send_notify()
    {
        $tickets = TicketsORM::whereNull('is_re_notify')->get();
        foreach ($tickets as $ticket) {
            if (isset($ticket->theme) && $ticket->theme->need_response == 1) {
                $notification = NotificationCronORM::query()
                    ->where('ticket_id', '=', $ticket->id)
                    ->where('is_complited', '=', '1')
                    ->first();
                if (!$notification) {
                    $cron = [
                        'ticket_id' => $ticket->id,
                        'is_complited' => 0
                    ];
                    $this->NotificationsCron->add($cron);
                    $ticket->update(['is_re_notify' => 1]);
                }
            }
        }
    }

}

new TicketNotificationCron();
