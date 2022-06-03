<?php

class TicketController extends Controller
{
    public function fetch()
    {
        if ($this->request->post('action')) {
            switch ($this->request->post('action', 'string')) :
                case 'accept_ticket':
                    $this->action_accept_ticket();
                    break;

                case 'close_ticket':
                    $this->action_close_ticket();
                    break;
            endswitch;
        }

        $ticket_id = (int)$this->request->get('id');

        $ticket = $this->Tickets->get_ticket($ticket_id);

        foreach ($ticket->docs as $key => $files) {
            $size = filesize(ROOT . "/files/users/" . $files->file_name);
            $size = number_format($size / 1048576, 2) . ' MB';
            $files->size = $size;
        }

        $this->design->assign('ticket', $ticket);

        return $this->design->fetch('ticket.tpl');
    }

    private function action_accept_ticket()
    {
        $ticket_id = (int)$this->request->post('ticket_id');
        $executor = $this->manager->id;

        $result = $this->Tickets->update_ticket($ticket_id, ['status' => 2, 'executor' => $executor]);

        if ($result === true) {
            echo 'success';
        } else {
            echo 'error';
        }

        exit;
    }

    private function action_close_ticket()
    {
        $ticket_id = (int)$this->request->post('ticket_id');

        $result = $this->Tickets->update_ticket($ticket_id, ['status' => 6]);

        if ($result === true) {
            echo 'success';
        } else {
            echo 'error';
        }

        exit;
    }
}
