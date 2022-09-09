<?php

use App\Services\MailService;

error_reporting(-1);
ini_set('display_errors', 'Off');

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

                    case 'telegram_hook':
                        $this->action_telegram_hook();
                        break;

                    case 'viber_hook':
                        $this->action_viber_hook();
                        break;

                    case 'add_credentials':
                        $this->action_add_credentials();
                        break;

                    case 'linkin_email':
                        $this->action_linkin_email();
                        break;

                    case 'confirm_linkin_email':
                        $this->action_confirm_linkin_email();
                        break;

                    case 'linkin_phone':
                        $this->action_linkin_phone();
                        break;

                    case 'confirm_linkin_phone':
                        $this->action_confirm_linkin_phone();
                        break;

                    case 'sms_note_flag':
                        $this->action_sms_note_flag();
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
                $user->sms_note = 1;
                $user->email_note = 1;
                $user->viber_note = $this->request->post('viber_note');
                $user->whatsapp_note = $this->request->post('whatsapp_note');
                $user->timezone = $this->request->post('timezone');
                $user->email_confirmed = $this->request->post('email_linked');
                $user->phone_confirmed = $this->request->post('phone_linked');

                if(empty($user->email_confirmed))
                    unset($user->email_confirmed);

                if(empty($user->phone_confirmed))
                    unset($user->phone_confirmed);

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
                                    'manager_id' => $user->id,
                                    'company_id' => $company['company_id']
                                ];
                            $this->ManagersEmployers->add_record($record);
                        }

                        $this->design->assign('message_success', 'added');
                        $to_manager = $user->id;
                    } else {
                        $manager = $this->managers->get_manager($user_id);
                        $user->id = $this->managers->update_manager($user_id, $user);

                        if($manager->phone != $user->phone)
                        {
                            $this->TelegramUsers->delete($user_id, 1);
                            $this->ViberUsers->delete($user_id, 1);
                            $this->managers->update_manager($user_id, ['telegram_note' => 0, 'viber_note' => 0]);
                        }

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
                        $to_manager = $user->id;
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

        if (isset($id)) {
            $managers_company = $this->ManagersEmployers->get_records($id);
        } else {
            $managers_company = $this->ManagersEmployers->get_records($this->manager->id);
        }
        $this->design->assign('managers_company', $managers_company);

        $this->design->assign('groups', $groups);

        $managers_credentials = $this->ManagersCredentials->gets(['manager_id' => $id]);

        if (!empty($managers_credentials)) {
            foreach ($managers_credentials as $credential) {
                $company = $this->companies->get_company($credential->company_id);
                $credential->company_name = $company->name;

                if ($credential->type == 'permanently')
                    $credential->type = 'Постоянный';
                else
                    $credential->type = 'Временный по доверенности';
            }
        }
        $this->design->assign('managers_credentials', $managers_credentials);

        if(isset($to_manager)){
            header('Location: '.$this->config->back_url.'/manager/'.$to_manager);
            exit;
        }

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
        $companies = $this->Companies->get_companies(['group_id' => $group_id]);
        $user_id = $this->request->post('user_id');
        $html = '';

        if (!empty($user_id) && $this->manager->id != $user_id)
            $managers_company = $this->ManagersEmployers->get_records($user_id);

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

        $this->db->query('
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
        $message = "Подтвердите Ваш номер телефона. Код подтверждения: " . $code;
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

    private function action_telegram_hook()
    {
        $manager_id = $this->request->post('user');
        $flag = $this->request->post('flag');

        $is_manager = 1;
        $check_telegram_hook = $this->TelegramUsers->get($manager_id, $is_manager);

        if (empty($check_telegram_hook)) {
            $manager = $this->managers->get_manager($manager_id);
            $user_token = md5(time());
            $user_token = substr($user_token, 1, 10);

            $phone = $manager->phone;
            $template = $this->sms->get_template(5);
            $message = str_replace('$user_token', $user_token, $template->template);

            $resp = $this->sms->send($phone, $message);

            $user =
                [
                    'user_id' => $manager_id,
                    'token' => $user_token,
                    'is_manager' => 1
                ];

            $this->TelegramUsers->add($user);

            $log =
                [
                    'user_id'    => $manager_id,
                    'is_manager' => 1,
                    'type_id'    => 1,
                    'resp'       => json_encode($resp),
                    'text'       => $message
                ];

            $this->NotificationsLogs->add($log);

            echo json_encode(['info' => 1]);
        }

        $this->managers->update_manager($manager_id, ['telegram_note' => $flag]);

        exit;
    }

    private function action_viber_hook()
    {
        $manager_id = $this->request->post('user');
        $flag = $this->request->post('flag');

        $is_manager = 1;
        $check_viber_hook = $this->ViberUsers->get($manager_id, $is_manager);

        if (empty($check_viber_hook)) {
            $manager = $this->managers->get_manager($manager_id);
            $user_token = md5(time());
            $user_token = substr($user_token, 1, 10);

            $mail = new PHPMailer(false);

            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'mail.nic.ru';                          //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = 'noreply@re-aktiv.ru';                  //SMTP username
            $mail->Password = 'HG!_@H#*&!^!HwJSDJ2Wsqgq';             //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
            $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('noreply@re-aktiv.ru');
            $mail->addAddress($manager->email);     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'RuCred | Уведомление';
            $mail->Body = '<h1>'.$this->config->back_url.'/redirect_api?user_id=' . $manager_id . '</h1>';

            $mail->send();

            $user =
                [
                    'user_id' => $manager_id,
                    'token' => $user_token,
                    'is_manager' => 1
                ];

            $this->ViberUsers->add($user);

            $log =
                [
                    'user_id'    => $manager_id,
                    'is_manager' => 1,
                    'type_id'    => 1,
                    'text'       => $this->config->back_url.'/redirect_api?user_id=' . $manager_id
                ];

            $this->NotificationsLogs->add($log);

            echo json_encode(['info' => 1]);
        }

        $this->managers->update_manager($manager_id, ['viber_note' => $flag]);
        exit;
    }

    private function action_add_credentials()
    {
        $manager_id = $this->request->post('manager_id');
        $company_id = $this->request->post('company');
        $type = $this->request->post('type');
        $expiration = $this->request->post('expiration');
        //$document = $_FILES['document'];

        $credentials =
            [
                'manager_id' => $manager_id,
                'company_id' => $company_id,
                'type' => $type,
                'expiration' => date('Y-m-d', strtotime($expiration))
            ];

        $this->ManagersCredentials->add($credentials);
        exit;
    }

    private function action_linkin_email()
    {
        $email = $this->request->post('email');

        $code = random_int(1000, 9999);

        $this->db->query('
        INSERT INTO s_email_messages
        SET email = ?, code = ?, created = ?
        ', $email, $code, date('Y-m-d H:i:s'));

        $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
        $mailService->send(
            'rucred@ucase.live',
            $email,
            'RuCred | Ваш проверочный код для смены почты',
            'Введите этот код в поле для проверки почты: ' . $code,
            '<h1>Введите этот код в поле для проверки почты:</h1>' . "<h2>$code</h2>"
        );

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_confirm_linkin_email()
    {
        $code = $this->request->post('code');
        $email = $this->request->post('email');

        $this->db->query("
        SELECT code, created
        FROM s_email_messages
        WHERE email = ?
        AND code = ?
        ORDER BY created DESC
        LIMIT 1
        ", $email, $code);

        $result = $this->db->result();

        if (empty($result)) {
            echo json_encode(['error' => 1]);
            exit;
        }

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_linkin_phone()
    {
        $phone = $this->request->post('phone');
        $code = random_int(1000, 9999);

        $message = "Подтвердите Ваш номер телефона. Код подтверждения: " . $code;
        $this->sms->send(
            $phone,
            $message
        );

        $query = $this->db->placehold("
        INSERT INTO s_sms_messages
        SET phone = ?, code = ?, created = ?
        ", $phone, $code, date('Y-m-d H:i:s'));

        $this->db->query($query);

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_confirm_linkin_phone()
    {
        $phone = $this->request->post('phone');
        $code = $this->request->post('code');
        $userId = $this->request->post('userId');

        $query = $this->db->placehold("
        SELECT *
        FROM s_sms_messages
        WHERE phone = ?
        AND code = ?
        ORDER BY id DESC
        LIMIT 1
        ", $phone, $code);

        $this->db->query($query);

        $result = $this->db->result();
        if (empty($result)) {
            echo json_encode(['error' => 1]);
            exit;
        }

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_sms_note_flag()
    {
        $flag = $this->request->post('value');
        $manager_id = $this->request->post('user_id');

        $this->managers->update_manager($manager_id, ['sms_note' => $flag]);
        exit;
    }
}
