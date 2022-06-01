<?php

class TicketsController extends Controller
{
    public function fetch()
    {
        if ($this->request->post('action')) {
            switch ($this->request->post('action', 'string')) :
                case 'add_ticket':
                    $this->action_add_ticket();
                    break;

                case 'get_companies':
                    $this->action_get_companies();
                    break;

                case 'search_user':
                    return $this->action_search_user();
                    break;
            endswitch;
        }else{
            $manager_role = $this->manager->role;
            $manager_id = $this->manager->id;

            if ($this->request->get('in')) {
                $in = true;
                $this->design->assign('in', $in);
                $in_out = 'in';
            }
            if ($this->request->get('out')) {
                $out = true;
                $this->design->assign('out', $out);
                $in_out = 'out';
            }

            if ($this->request->get('archive')) {
                $archive = true;
                $this->design->assign('archive', $archive);
                $in_out = 'archive';
            }

            $tickets = $this->Tickets->get_tickets($manager_role, $manager_id, $in_out);
            $this->design->assign('tickets', $tickets);

            $groups = $this->Groups->get_groups();
            $this->design->assign('groups', $groups);

            return $this->design->fetch('tickets.tpl');
        }
    }

    private function action_add_ticket()
    {
        $group_id = (int)$this->request->post('groups');
        $company_id = (int)$this->request->post('companies');
        $lastname = $this->request->post('lastname');
        $firstname = $this->request->post('firstname');
        $patronymic = $this->request->post('patronymic');
        $birth = $this->request->post('birth');
        $text = $this->request->post('text');
        $head = $this->request->post('head');
        $manager_id = (int)$this->request->post('manager_id');

        $ticket =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'client_lastname' => $lastname,
                'client_firstname' => $firstname,
                'client_patronymic' => $patronymic,
                'client_birth' => date('Y-m-d', strtotime($birth)),
                'manager_id' => $manager_id,
                'text' => $text,
                'head' => $head
            ];

        $ticket_id = $this->Tickets->add_ticket($ticket);

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
                $new_filename = md5(microtime() . rand()) . '.' . $file['name'];

                move_uploaded_file($file['tmp_name'], $this->config->root_dir . $this->config->user_files_dir . $new_filename);

                $new_file =
                    [
                        'ticket_id' => (int)$ticket_id,
                        'file_name' => $new_filename
                    ];

                $this->TicketsDocs->add_doc($new_file);
            }
        }
    }

    private function action_get_companies()
    {
        $group_id = (int)$this->request->post('group_id');
        $companies = $this->Companies->get_companies(['group_id' => $group_id]);

        $html = '<label class="control-label">Компании:</label>';

        foreach ($companies as $company) {
            $html .= '<div class="form-group">';
            $html .= '<input type="checkbox" class="custom-checkbox"';
            $html .= 'name="companies[][company_id]"';
            $html .= "value='$company->id'>";
            $html .= ' <label>' . $company->name . '</label>';
            $html .= '</div>';

        }
        echo $html;
        exit;
    }

    private function action_search_user()
    {
        $lastname = $this->request->post('lastname');
        $users = $this->users->get_user_by_lastname($lastname);
        $autocomplete = array();

        foreach ($users as $user){
            $autocomplete['suggestions'][] = "$user->personal_number $user->lastname $user->firstname $user->patronymic";
        }
        echo json_encode($autocomplete);
        exit;
    }
}
