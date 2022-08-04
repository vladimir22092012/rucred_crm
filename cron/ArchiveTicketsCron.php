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
        $tickets = $this->Tickets->gets();
        $now = new DateTime(date('Y-m-d'));

        foreach ($tickets as $ticket) {
            $created = date('Y-m-d', strtotime($ticket->created));
            $ticket_created = new DateTime($created);
            $archive = 0;

            if ($ticket_created > $now && date_diff($now, $ticket_created)->days >= 2) {
                $need_response = $this->CommunicationsThemes->get($ticket->theme_id);

                if (empty($need_response)) {
                    $notes = $this->TicketsNotes->gets($ticket->id);

                    if (!empty($notes)) {
                        $archive = 1;
                    }
                }

                if ($need_response == 1 && $ticket->status == 4) {
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