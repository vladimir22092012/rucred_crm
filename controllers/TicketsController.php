<?php

error_reporting(-1);
ini_set('display_errors', 'On');
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

                case 'qr':
                    return $this->action_qr();
                    break;
            endswitch;
        } else {
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

            $filter_status = $this->request->get('status');

            if (in_array($filter_status, ['0',2,4])) {
                $this->design->assign('filter_status', $filter_status);
            }else{
                $filter_status = 'false';
            }

            $sort = 't.id desc';

            if ($this->request->get('sort', 'string')) {
                $sort = $this->request->get('sort', 'string');
                $this->design->assign('sort', $sort);
            }

            $tickets = $this->Tickets->get_tickets($manager_role, $manager_id, $in_out, ['sort' => $sort], $filter_status);

            foreach ($tickets as $key => $ticket) {

                if($this->manager->created > $ticket->created)
                {
                    unset($tickets[$key]);
                    continue;
                }

                $ticket->new = 0;
                $ticket_new = $this->TicketsNotes->get($ticket->id, $manager_id);

                if(empty($ticket_new))
                    $ticket->new = 1;

                if ($ticket->executor != 0) {
                    $manager = $this->managers->get_manager($ticket->executor);
                    $ticket->executor = array();
                    $ticket->executor['name'] = $manager->name;
                    $ticket->executor['id'] = $manager->id;
                }

                if($ticket->creator_company)
                    $company = $this->Companies->get_company($ticket->creator_company);
                else{
                    $company = new stdClass();
                    $company->name = 'Отсутствует';
                }

                $ticket->creator_company_name = $company->name;
                $ticket->number = str_pad($ticket->id, 6, '0', STR_PAD_LEFT);

                if (!empty($ticket->order_id)) {
                    $ticket->order = $this->orders->get_order($ticket->order_id);

                    if (!empty($ticket->order->contract_id)){
                        $ticket->contract = $this->contracts->get_contract($ticket->order->contract_id);
                    }
                }
            }

            $themes = $this->CommunicationsThemes->gets(['sort' => 'id DESC', 'create_ticket' => [41,42]]);
            $this->design->assign('themes', $themes);

            $this->design->assign('tickets', $tickets);

            $groups = $this->Groups->get_groups();
            $this->design->assign('groups', $groups);

            $managers_companies = $this->ManagersEmployers->get_records($this->manager->id);
            $this->design->assign('managers_companies', $managers_companies);

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
        $text = $this->request->post('text');
        $theme_id = $this->request->post('theme');
        $manager_id = (int)$this->request->post('manager_id');
        $creator_company = (int)$this->request->post('creator_company');

        $ticket =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'client_lastname' => $lastname,
                'client_firstname' => $firstname,
                'client_patronymic' => $patronymic,
                'creator' => $manager_id,
                'creator_company' => $creator_company,
                'text' => $text,
                'theme_id' => $theme_id
            ];

        $ticket_id = $this->Tickets->add_ticket($ticket);

        $message =
            [
                'message' => $text,
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
                        'message_id' => $message_id,
                        'ticket_id' => (int)$ticket_id,
                        'file_name' => $file['name']
                    ];

                $this->TicketsDocs->add_doc($new_file);
            }
        }

        exit;
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

        foreach ($users as $user) {
            $autocomplete['suggestions'][] = "$user->personal_number $user->lastname $user->firstname $user->patronymic";
        }
        echo json_encode($autocomplete);
        exit;
    }

    private function action_qr()
    {
        $this->QrGenerateApi->get_qr(15000, 600);
        exit;
    }
}
