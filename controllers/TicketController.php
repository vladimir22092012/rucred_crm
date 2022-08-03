<?php

error_reporting(-1);
ini_set('display_errors', 'On');

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

                case 'add_message':
                    $this->action_add_message();
                    break;

                case 'return_ticket':
                    $this->action_return_ticket();
                    break;
            endswitch;
        }

        $ticket_id = (int)$this->request->get('id');

        $get_note = $this->TicketsNotes->get($ticket_id, $this->manager->id);

        if (empty($get_note)) {
            $note =
                [
                    'ticket_id' => $ticket_id,
                    'user_id' => $this->manager->id
                ];

            $this->TicketsNotes->add($note);
        }

        $ticket = $this->Tickets->get_ticket($ticket_id);

        if (in_array($ticket->theme_id, [12, 37]) && $this->manager->role == 'middle') {
            if (empty($ticket->executor)) {
                $this->Tickets->update_ticket($ticket_id, ['executor' => $this->manager->id]);
            }
        }

        if($this->manager->role == 'employer' && $ticket->creator != $this->manager->id && empty($ticket->executor))
            $this->Tickets->update_ticket($ticket_id, ['executor' => $this->manager->id]);

        if(!empty($ticket->executor)){
            $manager = $this->managers->get_manager($ticket->executor);
            if($this->manager->role == $manager->role && $this->manager->id != $manager->id){
                $can_take_it = 1;
                $this->design->assign('can_take_it', $can_take_it);
            }
        }

        if(!empty($ticket->order_id)){
            $order = $this->orders->get_order($ticket->order_id);
            $this->design->assign('offline', $order->offline);
        }

        $messages = $this->TicketMessages->get_messages($ticket_id);

        $this->design->assign('messages', $messages);

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

        $this->TicketsNotes->delete($ticket_id);

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

        $this->TicketsNotes->delete($ticket_id);

        $result = $this->Tickets->update_ticket($ticket_id, ['status' => 6]);

        if ($result === true) {
            echo 'success';
        } else {
            echo 'error';
        }

        exit;
    }

    private function action_add_message()
    {
        $ticket_id = $this->request->post('ticket_id');
        $manager_id = $this->request->post('manager_id');
        $message = $this->request->post('message');

        $message =
            [
                'message' => $message,
                'ticket_id' => $ticket_id,
                'manager_id' => $manager_id,
            ];

        $message_id = $this->TicketMessages->add_message($message);

        if (!empty($_FILES['docs']['name'][0])) {
            function files($name, $type, $tmp_name, $error, $size)
            {
                return [
                    'name' => $name,
                    'type' => $type,
                    'tmp_name' => $tmp_name,
                    'error' => $error,
                    'size' => $size
                ];
            }

            $files = array_map('files', $_FILES['docs']['name'], $_FILES['docs']['type'], $_FILES['docs']['tmp_name'], $_FILES['docs']['error'], $_FILES['docs']['size']);

            foreach ($files as $file) {

                move_uploaded_file($file['tmp_name'], $this->config->root_dir . $this->config->user_files_dir . $file['name']);

                $new_file =
                    [
                        'ticket_id' => (int)$ticket_id,
                        'file_name' => $file['name'],
                        'message_id' => $message_id
                    ];

                $this->TicketsDocs->add_doc($new_file);
            }
        }

        $ticket = $this->Tickets->get_ticket($ticket_id);

        $this->TicketsNotes->delete($ticket_id);

        if ($ticket->executor == $manager_id)
            $this->Tickets->update_ticket($ticket_id, ['status' => 3]);
    }

    private function action_return_ticket()
    {

        $ticket_id = (int)$this->request->post('ticket_id');

        $this->TicketsNotes->delete($ticket_id);
        $result = $this->Tickets->update_ticket($ticket_id, ['status' => 5]);

        if ($result === true) {
            echo 'success';
        } else {
            echo 'error';
        }

        exit;
    }
}
