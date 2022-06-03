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

                case 'add_message':
                    $this->action_add_message();
                    break;
            endswitch;
        }

        $ticket_id = (int)$this->request->get('id');

        $ticket = $this->Tickets->get_ticket($ticket_id);

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

        $result = $this->Tickets->update_ticket($ticket_id, ['status' => 2, 'executor' => $executor, 'note_flag' => 1]);

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
    }
}
