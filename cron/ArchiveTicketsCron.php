<?php
error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('Europe/Moscow');


chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class ArchiveTicketsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {

        //$tickets = $this->Tickets->gets();

        //Текущая дата и время
        $now  = time();
        $nowStr = date('Y-m-d 18:00:00', $now);

        //Дата и время тикета
        $created = '2023-02-17 17:00:00';
        $ticket_created = strtotime($created);

        //Получаем кол-во не рабочих дней
        $datesArray = \App\Helpers\DateTimeHelpers::getDatesArray($created, $nowStr);
        $weekendCalendarDays = WeekendCalendarORM::query()->whereIn('date', $datesArray)->get();

        $hours = round((($now - $ticket_created) / 60) / 60, 0) - (count($weekendCalendarDays) * 24);
        var_dump($hours >= 72);

        die();

        foreach ($tickets as $ticket) {

            $created = date('Y-m-d', strtotime($ticket->created));
            $ticket_created = new DateTime($created);
            $archive = 0;

            $days = date_diff($now, $ticket_created)->days;
            $datesArray = \App\Helpers\DateTimeHelpers::getDatesArray($created, $nowStr);
            $weekendCalendarDays = WeekendCalendarORM::query()->whereIn('date', $datesArray)->get();
            if (count($weekendCalendarDays) > 0) {
                $days -= count($weekendCalendarDays);
            }

            if ($days >= 3) {
                $need_response = $this->CommunicationsThemes->get($ticket->theme_id);

                if (empty($need_response->need_response)) {
                    $notes = $this->TicketsNotes->gets($ticket->id);

                    if (!empty($notes)) {
                        $archive = 1;
                    }
                }

                if ($need_response->need_response == 1 && $ticket->status == 4) {
                    $archive = 1;
                }

                if ($archive == 1) {
                    $this->TicketsNotes->delete($ticket->id);
                    $this->Tickets->update_ticket($ticket->id, ['status' => 6]);
                }
            }
        }
    }
}

new ArchiveTicketsCron();
