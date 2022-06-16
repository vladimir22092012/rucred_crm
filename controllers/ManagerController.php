<?php

use App\Services\MailService;

class ManagerController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            if ($this->request->post('action', 'string')) {
                switch ($this->request->post('action', 'string')) :
                    case 'activate_email':
                        $this->action_activate_email();
                        break;

                    case 'get_companies':
                        $this->action_get_companies();
                        break;

                    case 'edit_email':
                        $this->action_edit_email();
                        break;

                    case 'edit_email_with_code':
                        $this->action_edit_email_with_code();
                        break;

                    case 'edit_phone':
                        $this->action_edit_phone();
                        break;

                    case 'edit_phone_with_code':
                        $this->action_edit_phone_with_code();
                        break;

                    case 'edit_password':
                        $this->action_edit_password();
                        break;

                    case 'block_manager':
                        $this->action_block_manager();
                        break;

                    case 'delete_manager':
                        $this->action_delete_manager();
                        break;

                    case 'edit_login':
                        $this->action_edit_login();
                        break;
                endswitch;
            } else {
                $user = new StdClass();

                $user_id = $this->request->post('id', 'integer');
                $user->role = $this->request->post('role');
                $user->name = $this->request->post('name');
                $user->name_1c = $this->request->post('name_1c');
                $user->email = $this->request->post('email');
                $user->phone = $this->request->post('phone');
                $user->login = $this->request->post('login');
                $user->mango_number = $this->request->post('mango_number');
                $user->telegram_note = $this->request->post('telegram_note');
                $user->sms_note = $this->request->post('sms_note');
                $user->viber_note = $this->request->post('viber_note');
                $user->whatsapp_note = $this->request->post('whatsapp_note');

                $same_login = $this->Managers->check_same_login($user->login, $user_id);

                $user->group_id = ($this->request->post('groups')) ? (int)$this->request->post('groups') : 0;
                $companies = $this->request->post('companies');
                $user->collection_status_id = $this->request->post('collection_status_id', 'integer');

                $team_id = (array)$this->request->post('team_id');

                if (!empty($team_id)) {
                    $user->team_id = implode(',', $team_id);
                }

                if ($this->request->post('password')) {
                    $user->password = $this->request->post('password');
                }

                $errors = array();

                if (empty($user->role)) {
                    $errors[] = 'empty_role';
                }
                if (!empty($same_login)) {
                    $errors[] = 'login_exists';
                }
                if (empty($user->name)) {
                    $errors[] = 'empty_name';
                }
                if (empty($user->login)) {
                    $errors[] = 'empty_login';
                }

                if (empty($user_id) && empty($user->password)) {
                    $errors[] = 'empty_password';
                }

                $this->design->assign('errors', $errors);

                if (!$errors) {
                    if (empty($user_id)) {
                        $user->id = $this->managers->add_manager($user);
                        foreach ($companies as $company) {
                            $record =
                                [
                                    'manager_id' => $user_id,
                                    'company_id' => $company['company_id']
                                ];
                            $this->ManagersEmployers->add_record($record);
                        }
                        $this->design->assign('message_success', 'added');
                    } else {
                        $user->id = $this->managers->update_manager($user_id, $user);
                        $this->ManagersEmployers->delete_records($user_id);
                        foreach ($companies as $company) {
                            $record =
                                [
                                    'manager_id' => $user_id,
                                    'company_id' => $company['company_id']
                                ];
                            $this->ManagersEmployers->add_record($record);
                        }
                        $this->design->assign('message_success', 'updated');
                    }
                    $user = $this->managers->get_manager($user->id);
                }
            }
        } else {
            if ($this->request->get('action') == 'blocked') {
                $manager_id = $this->request->get('manager_id', 'integer');
                $block = $this->request->get('block', 'integer');

                $this->managers->update_manager($manager_id, array('blocked' => $block));

                /*
                                if ($contracts = $this->contracts->get_contracts(array('collection_manager_id'=>$manager_id)))
                                {
                                    foreach ($contracts as $c)
                                    {
                                        $this->contracts->update_contract($c->id, array('collection_manager_id'=>0, 'collection_workout'=>0));
                                        $this->users->update_user($contract->user_id, array('contact_status' => 0));
                                    }
                //                    $this->contracts->distribute_contracts();
                                }

                                exit;
                */
            }

            if ($id = $this->request->get('id', 'integer')) {
                $user = $this->managers->get_manager($id);
            }
        }

        if (!empty($user)) {
            $meta_title = 'Профиль ' . $user->name;
            $this->design->assign('user', $user);
        } else {
            $meta_title = 'Создать новый профиль';
        }

        $roles = $this->managers->get_roles();
        $this->design->assign('roles', $roles);

        $collection_statuses = $this->contracts->get_collection_statuses();
        $this->design->assign('collection_statuses', $collection_statuses);

        $collection_manager_statuses = array();
        $managers = array();
        foreach ($this->managers->get_managers() as $m) {
            $managers[$m->id] = $m;
            $collection_manager_statuses[] = $m->collection_status_id;
        }
        $this->design->assign('managers', $managers);
        $collection_manager_statuses = array_filter(array_unique($collection_manager_statuses));
        $this->design->assign('collection_manager_statuses', $collection_manager_statuses);

        $this->design->assign('meta_title', $meta_title);

        $groups = $this->Groups->get_groups();

        if (isset($user) && $user->group_id != 0) {
            $companies = $this->Companies->get_companies(['group_id' => $user->group_id]);
            $this->design->assign('companies', $companies);
        }

        $managers_company = $this->ManagersEmployers->get_records($this->manager->id);
        $this->design->assign('managers_company', $managers_company);

        $this->design->assign('groups', $groups);

        if ($this->request->get('main')) {
            $lk = true;
            $this->design->assign('lk', $lk);
        }

        return $this->design->fetch('manager.tpl');
    }

    private function action_activate_email()
    {
        $email = $this->request->post('email');
        $token = sha1(uniqid($email, true));

        var_dump($token);
        exit;
    }

    private function action_get_companies()
    {

        $group_id = $this->request->post('group_id');
        $user_id = $this->request->post('user_id');
        $companies = $this->Companies->get_companies(['group_id' => $group_id]);
        $managers_company = $this->ManagersEmployers->get_records($user_id);
        $html = '';

        foreach ($companies as $company) {
            $html .= '<div class="form-group">';
            $html .= '<input type="checkbox" class="custom-checkbox"';
            $html .= 'name="companies[][company_id]"';
            $html .= "value='$company->id'";
            $html .= (isset($managers_company[$company->id])) ? 'checked> ' : '> ';
            $html .= '<label>' . $company->name . '</label>';
            $html .= '</div>';

        }
        echo $html;
        exit;
    }

    private function action_edit_email()
    {
        $email = $this->request->post('email');
        $user_id = $this->request->post('user_id');
        $code = random_int(1000, 9999);

        $result = $this->db->query('
        INSERT INTO s_email_messages
        SET user_id = ?, email = ?, code = ?, created = ?
        ', $user_id, $email, $code, date('Y-m-d H:i:s'));

        $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
        $mailResponse = $mailService->send(
            'rucred@ucase.live',
            $email,
            'RuCred | Ваш проверочный код для смены почты',
            'Введите этот код в поле для проверки почты: ' . $code,
            '<h1>Введите этот код в поле для проверки почты:</h1>' . "<h2>$code</h2>"
        );

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_email_with_code()
    {
        $user_id = $this->request->post('user_id');
        $email = $this->request->post('email');
        $code = $this->request->post('code');

        $this->db->query("
        SELECT code, created
        FROM s_email_messages
        WHERE user_id = ?
        AND email = ?
        AND code = ?
        ORDER BY created DESC
        LIMIT 1
        ", $user_id, $email, $code);
        $results = $this->db->results();
        if (empty($results)) {
            echo json_encode(['error' => 1]);
            exit;
        }
        $result = $this->managers->update_manager($user_id, ['email' => $email]);
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_phone()
    {
        $phone = $this->request->post('phone');
        $user_id = $this->request->post('user_id');
        $code = random_int(1000, 9999);
        $message = "Подтвердите Ваш номер телефона. Код подтверждения: ".$code;
        $response = $this->sms->send(
            $phone,
            $message
        );
        $this->db->query('
        INSERT INTO s_sms_messages
        SET phone = ?, code = ?, response = ?, ip = ?, user_id = ?, created = ?
        ', $phone, $code, $response['resp'], $_SERVER['REMOTE_ADDR'] ?? '', $user_id, date('Y-m-d H:i:s'));
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_phone_with_code()
    {
        $phone = $this->request->post('phone');
        $code = $this->request->post('code');
        $user_id = $this->request->post('user_id');

        $this->db->query("
        SELECT code, created
        FROM s_sms_messages
        WHERE phone = ?
        AND code = ?
        AND user_id = ?
        ORDER BY created DESC
        LIMIT 1
        ", $phone, $code, $user_id);

        $results = $this->db->results();
        if (empty($results)) {
            echo json_encode(['error' => 1]);
            exit;
        }

        $this->managers->update_manager($user_id, ['phone' => $phone]);
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_password()
    {
        $user_id = $this->request->post('user_id');
        $old_password = $this->request->post('old_password');
        $new_password = $this->request->post('new_password');

        $result = $this->managers->check_password_by_id($user_id, $old_password);

        if (empty($result)) {
            echo json_encode(['error' => 1]);
            exit;
        }
        $this->managers->update_manager($user_id, ['password' => $new_password]);
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_block_manager()
    {
        $flag = $this->request->post('value');
        $manager_id = $this->request->post('manager_id');

        $this->managers->update_manager($manager_id, ['blocked' => $flag]);
    }

    private function action_delete_manager()
    {
        $manager_id = $this->request->post('manager_id');

        $orders = $this->orders->get_orders(['manager_id' => $manager_id]);

        if (!empty($orders)) {
            echo 'У пользователя есть сделки!';
            exit;
        } else {
            echo 'success';
            $this->managers->delete_manager($manager_id);
            exit;
        }
    }

    private function action_edit_login()
    {
        $manager_id = $this->request->post('manager_id');
        $login = $this->request->post('login');

        $this->managers->update_manager($manager_id, ['login' => $login]);
    }
}
