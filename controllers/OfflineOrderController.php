<?php

use App\Services\MailService;
use App\Services\Encryption;

error_reporting(-1);
ini_set('display_errors', 'On');

class OfflineOrderController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            $order_id = $this->request->post('order_id', 'integer');
            $action = $this->request->post('action', 'string');

            switch ($action):

                case 'change_manager':
                    $this->change_manager_action();
                    break;

                case 'accept_by_employer':
                    $this->action_accept_by_employer();
                    break;

                case 'reject_by_employer':
                    $this->action_reject_by_employer();
                    break;

                case 'change_photo_status':
                    $this->action_change_photo_status();
                    break;

                case 'edit_schedule':
                    return $this->action_edit_schedule();
                    break;

                case 'fio':
                    $this->fio_action();
                    break;

                case 'contactdata':
                    $this->contactdata_action();
                    break;

                case 'contacts':
                    $this->contacts_action();
                    break;

                case 'addresses':
                    $this->addresses_action();
                    break;

                case 'work':
                    $this->work_action();
                    break;

                case 'amount':
                    $this->action_amount();
                    break;

                case 'change_loan_settings':
                    $this->action_change_loan_settings();
                    break;

                case 'inn_change':
                    $this->action_inn_change();
                    break;

                case 'snils_change':
                    $this->action_snils_change();
                    break;

                case 'cors_change':
                    $this->action_cors_change();
                    break;

                case 'cards':
                    $this->action_cards();
                    break;

                case 'contact_status':
                    $response = $this->action_contact_status();
                    $this->json_output($response);
                    break;

                case 'contactperson_status':
                    $response = $this->action_contactperson_status();
                    $this->json_output($response);
                    break;

                case 'status':
                    $status = $this->request->post('status', 'integer');
                    $response = $this->status_action($status);
                    $this->json_output($response);
                    break;

                // принять заявку
                case 'accept_order':
                    $response = $this->accept_order_action();
                    $this->json_output($response);
                    break;

                // одобрить заявку
                case 'approve_order':
                    $this->approve_order_action();
                    break;

                // совершить выплату по заявку
                case 'delivery_order':
                    $response = $this->delivery_order_action();
                    $this->json_output($response);
                    break;

                // одобрить заявку
                case 'autoretry_accept':
                    $response = $this->autoretry_accept_action();
                    $this->json_output($response);
                    break;

                // отказать в заявке
                case 'reject_order':
                    $response = $this->reject_order_action();
                    $this->json_output($response);
                    break;

                // подтвердить контракт
                case 'confirm_contract':
                    $response = $this->confirm_contract_action();
                    $this->json_output($response);
                    break;

                case 'add_comment':
                    $this->action_add_comment();
                    break;

                case 'close_contract':
                    $this->action_close_contract();
                    break;

                case 'repay':
                    $this->action_repay();
                    break;

                case 'send_sms':
                    $this->send_sms_action();
                    break;


                case 'personal':
                    $this->action_personal();
                    break;

                case 'passport':
                    $this->action_passport();
                    break;

                case 'reg_address':
                    $this->reg_address_action();
                    break;

                case 'fakt_address':
                    $this->fakt_address_action();
                    break;

                case 'workdata':
                    $this->workdata_action();
                    break;

                case 'work_address':
                    $this->work_address_action();
                    break;

                case 'socials':
                    $this->socials_action();
                    break;

                case 'images':
                    $this->action_images();
                    break;

                case 'services':
                    $this->action_services();
                    break;

                case 'workout':
                    $this->action_workout();
                    break;

                case 'delete_order':
                    $this->action_delete_order();
                    break;

                case 'edit_personal_number':
                    return $this->action_edit_personal_number();
                    break;

                case 'change_employer_info':
                    return $this->action_change_employer_info();
                    break;

                case 'restruct_term':
                    return $this->action_restruct_term();
                    break;

                case 'do_restruct':
                    return $this->action_do_restruct();
                    break;

                case 'send_asp_code':
                    return $this->action_send_asp_code();
                    break;

                case 'confirm_asp':
                    return $this->action_confirm_asp();
                    break;

                case 'create_pay_rdr':
                    return $this->action_create_pay_rdr();
                    break;

                case 'send_qr':
                    return $this->action_send_qr();
                    break;

                case 'confirm_restruct':
                    return $this->action_confirm_restruct();
                    break;

                case 'upload_docs_to_yandex':
                    return $this->action_upload_docs_to_yandex();
                    break;


            endswitch;

        } else {
            $managers = array();
            foreach ($this->managers->get_managers() as $m)
                $managers[$m->id] = $m;

            $scoring_types = $this->scorings->get_types();

            $this->design->assign('scoring_types', $scoring_types);

            if ($order_id = $this->request->get('id', 'integer')) {
                if ($order = $this->orders->get_order($order_id)) {

                    $scroll_to_photo = $this->request->get('scroll');
                    $this->design->assign('scroll_to_photo', $scroll_to_photo);

                    $order->requisite = $this->requisites->get_requisite($order->requisite_id);

                    $holder = $order->requisite->holder;
                    $holder = explode(' ', $holder, 3);
                    $same_holder = 0;

                    if (count($holder) == 3) {
                        list($holder_name, $holder_firstname, $holder_patronymic) = $holder;
                        if ($order->lastname == $holder_name && $order->firstname == $holder_firstname && $order->patronymic == $holder_patronymic)
                            $same_holder = 1;
                    }

                    $this->design->assign('same_holder', $same_holder);

                    $enough_scans = 0;

                    $query = $this->db->placehold("
                    SELECT `type`
                    FROM s_scans
                    WHERE order_id = ?
                    AND `type` != 'ndfl'
                    ", (int)$order->order_id);

                    $this->db->query($query);
                    $scans = $this->db->results();

                    $managers_roles = $this->ManagerRoles->get();

                    foreach ($managers_roles as $role) {
                        if ($this->manager->role == $role->name)
                            $filter['role_id'] = $role->id;
                    }

                    $filter['order_id'] = $order_id;

                    $users_docs = $this->Documents->get_documents($filter);

                    if (count($scans) == count($users_docs))
                        $enough_scans = 1;

                    $this->design->assign('enough_scans', $enough_scans);

                    $client = $this->users->get_user($order->user_id);
                    $this->design->assign('client', $client);

                    $communications = $this->communications->get_communications(array('user_id' => $client->id));
                    $this->design->assign('communications', $communications);

                    // причина "не удалось выдать"
                    if ($order->status == 6) {
                        if ($p2p = $this->issuances->get_order_issuance($order->order_id)) {
                            $p2p->response = unserialize($p2p->response);
                            if (!empty($p2p->response))
                                $p2p->response_xml = simplexml_load_string($p2p->response);
                            $this->design->assign('p2p', $p2p);
                        }

                        if ($client_orders = $this->orders->get_orders(array('user_id' => $order->user_id))) {
                            $have_newest_order = 0;
                            foreach ($client_orders as $co) {
                                if ($co->order_id != $order->order_id && (strtotime($order->date) < strtotime($co->date)))
                                    $have_newest_order = $co->order_id;
                            }
                            $this->design->assign('have_newest_order', $have_newest_order);
                        }
                    }

                    $penalties = array();
                    foreach ($this->penalties->get_penalties(array('order_id' => $order_id)) as $p) {
                        if (in_array($p->status, array(1, 2, 4)))
                            $penalties[$p->block] = $p;
                    }
                    $this->design->assign('penalties', $penalties);
                    $groups = $this->Groups->get_groups();
                    $companies = $this->Companies->get_companies(['group_id' => $order->group_id]);

                    foreach ($groups as $group) {
                        if ($order->group_id == $group->id) {
                            $group_name = $group->name;
                            $order->group_number = $group->number;
                        }
                    }

                    if (!empty($order->company_id)) {

                        $branches = $this->Branches->get_branches(['company_id' => $order->company_id]);
                        $this->design->assign('branches', $branches);

                        if (!empty($order->branche_id)) {
                            foreach ($branches as $branch) {
                                if ($order->branche_id == $branch->id)
                                    $branch_name = $branch->name;
                            }
                        }

                        if (!empty($order->company_id)) {
                            foreach ($companies as $company) {
                                if ($order->company_id == $company->id) {
                                    $company_name = $company->name;
                                    $order->company_number = $company->number;
                                }
                            }
                        }
                    }

                    if (!isset($branch_name))
                        $branch_name = 'По умолчанию';

                    if (!isset($company_name))
                        $company_name = 'Отсутствует компания';

                    $this->design->assign('groups', $groups);
                    $this->design->assign('companies', $companies);
                    $this->design->assign('branch_name', $branch_name);
                    $this->design->assign('company_name', $company_name);
                    $this->design->assign('group_name', $group_name);
                    $this->design->assign('order', $order);

                    $comments = $this->comments->get_comments(array('user_id' => $order->user_id));
                    foreach ($comments as $comment) {
                        if (isset($managers[$comment->manager_id]))
                            $comment->letter = mb_substr($managers[$comment->manager_id]->name, 0, 1);
                    }
                    $this->design->assign('comments', $comments);

                    $files = $this->users->get_files(array('user_id' => $order->user_id));
                    foreach ($files as $file) {
                        $format = explode('.', $file->name);

                        if ($format[1] == 'pdf')
                            $file->format = 'PDF';
                    }
                    $this->design->assign('files', $files);


                    $user_close_orders = $this->orders->get_orders(array(
                        'user_id' => $order->user_id,
                        'type' => 'base',
                        'status' => array(7)
                    ));
                    $order->have_crm_closed = !empty($user_close_orders);


                    if (!empty($order->contract_id)) {
                        if ($contract_operations = $this->operations->get_operations(array('contract_id' => $order->contract_id)))
                            foreach ($contract_operations as $contract_operation)
                                if (!empty($contract_operation->transaction_id))
                                    $contract_operation->transaction = $this->transactions->get_transaction($contract_operation->transaction_id);
                        $this->design->assign('contract_operations', $contract_operations);

                        $contract = $this->contracts->get_contract((int)$order->contract_id);
                        $this->design->assign('contract', $contract);
                    }


                    $need_update_scorings = 0;
                    $inactive_run_scorings = 0;
                    $scorings = array();
                    if ($result_scorings = $this->scorings->get_scorings(array('order_id' => $order->order_id))) {
                        foreach ($result_scorings as $scoring) {
                            if ($scoring->type == 'juicescore') {
                                $scoring->body = unserialize($scoring->body);
                            }

                            if ($scoring->type == 'efrsb') {
                                $scoring->body = @unserialize($scoring->body);
                            }

                            if ($scoring->type == 'scorista') {
                                $scoring->body = json_decode($scoring->body);
                                if (!empty($scoring->body->equifaxCH))
                                    $scoring->body->equifaxCH = iconv('cp1251', 'utf8', base64_decode($scoring->body->equifaxCH));
                            }
                            if ($scoring->type == 'fssp') {
                                $scoring->body = @unserialize($scoring->body);
                                $scoring->found_46 = 0;
                                $scoring->found_47 = 0;
                                if (!empty($scoring->body->result[0]->result)) {
                                    foreach ($scoring->body->result[0]->result as $result) {
                                        if (!empty($result->ip_end)) {
                                            $ip_end = array_map('trim', explode(',', $result->ip_end));
                                            if (in_array(46, $ip_end))
                                                $scoring->found_46 = 1;
                                            if (in_array(47, $ip_end))
                                                $scoring->found_47 = 1;
                                        }
                                    }
                                }
                            }
                            if ($scoring->type == 'nbki') {
                                $scoring->body = unserialize($scoring->body);
                            }


                            $scorings[$scoring->type] = $scoring;

                            if ($scoring->status == 'new' || $scoring->status == 'process' || $scoring->status == 'repeat') {
                                $need_update_scorings = 1;
                                if (isset($scoring_types[$scoring->type]->type) && $scoring_types[$scoring->type]->type == 'first')
                                    $inactive_run_scorings = 1;
                            }
                        }
                        /*
                        $scorings['efsrb'] = (object)array(
                            'success' => 1,
                            'string_result' => 'Проверка пройдена',
                            'status' => 'completed',
                            'created' => $scoring->created
                        );
                        */
                    }
                    $this->design->assign('scorings', $scorings);
                    $this->design->assign('need_update_scorings', $need_update_scorings);
                    $this->design->assign('inactive_run_scorings', $inactive_run_scorings);

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scorings, $scoring_types);echo '</pre><hr />';

                    $user = $this->users->get_user((int)$order->user_id);
                    $user->regaddress = $this->addresses->get_address($user->regaddress_id);
                    $user->faktaddress = $this->addresses->get_address($user->faktaddress_id);

                    $this->design->assign('user', $user);

                    $changelogs = $this->changelogs->get_changelogs(array('order_id' => $order_id));
                    foreach ($changelogs as $changelog) {
                        $changelog->user = $user;
                        if (!empty($changelog->manager_id) && !empty($managers[$changelog->manager_id]))
                            $changelog->manager = $managers[$changelog->manager_id];
                    }
                    $changelog_types = $this->changelogs->get_types();

                    $this->design->assign('changelog_types', $changelog_types);
                    $this->design->assign('changelogs', $changelogs);

                    $eventlogs = $this->eventlogs->get_logs(array('order_id' => $order_id));
                    $this->design->assign('eventlogs', $eventlogs);

                    $events = $this->eventlogs->get_events();
                    $this->design->assign('events', $events);


                    if ($eventlogs) {
                        $html = '';
                        foreach ($eventlogs as $eventlog) {
                            $event = $events[$eventlog->event_id];
                            $event_created = $eventlog->created;
                            $manager_name = $managers[$eventlog->manager_id]->name;

                            $html = $html . "<tr><td>{$event_created}</td><td>{$event}</td><td>{$manager_name}</a></td></tr>";
                        }

                        $this->design->assign('html', "<table>$html</table>");
                    }


                    $cards = array();
                    foreach ($this->cards->get_cards(array('user_id' => $order->user_id)) as $card)
                        $cards[$card->id] = $card;
                    foreach ($cards as $card)
                        $card->duplicates = $this->cards->find_duplicates($order->user_id, $card->pan, $card->expdate);

                    $this->design->assign('cards', $cards);


                    $orders = array();
                    foreach ($this->orders->get_orders(array('user_id' => $order->user_id)) as $o) {
                        if (!empty($o->contract_id))
                            $o->contract = $this->contracts->get_contract($o->contract_id);

                        $orders[] = $o;
                    }
                    $this->design->assign('orders', $orders);

                    if (in_array('looker_link', $this->manager->permissions)) {
                        $looker_link = $this->users->get_looker_link($order->user_id);
                        $this->design->assign('looker_link', $looker_link);
                    }
                } else {
                    return false;
                }
            }
        }


        $scoring_types = array();
        foreach ($this->scorings->get_types(array('active' => true)) as $type)
            $scoring_types[$type->name] = $type;
        $this->design->assign('scoring_types', $scoring_types);

        $reject_reasons = array();
        foreach ($this->reasons->get_reasons() as $r)
            $reject_reasons[$r->id] = $r;
        $this->design->assign('reject_reasons', $reject_reasons);

        $order_statuses = $this->orders->get_statuses();
        $this->design->assign('order_statuses', $order_statuses);

        $penalty_types = $this->penalties->get_types();
        $this->design->assign('penalty_types', $penalty_types);

        $sms_templates = $this->sms->get_templates(array('type' => 'order'));
        $this->design->assign('sms_templates', $sms_templates);

        if ($this->request->post('create_documents')) {
            $order_id = $this->request->post('order_id');
            $this->form_docs($order_id);
        }

        $schedules = $this->PaymentsSchedules->gets($order_id);

        if (count($schedules) > 1) {

            foreach ($schedules as $key => $schedule) {
                $schedule->schedule = json_decode($schedule->schedule, true);

                uksort($schedule->schedule,
                    function ($a, $b) {

                        if ($a == $b)
                            return 0;

                        return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
                    });

                if ($schedule->actual == 1)
                    $payment_schedule = end($schedules);
            }
        } else {
            $payment_schedule = end($schedules);
            $payment_schedule->schedule = json_decode($payment_schedule->schedule, true);

            uksort($payment_schedule->schedule,
                function ($a, $b) {

                    if ($a == $b)
                        return 0;

                    return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
                });
        }

        foreach ($payment_schedule as $payday => $payment) {
            if ($payday != 'result') {
                $payday = date('Y-m-d', strtotime($payday));
                if ($payday > date('Y-m-d')) {
                    $next_payment = $payment['pay_sum'];
                    break;
                }
            }
        }

        if (isset($next_payment))
            $this->design->assign('next_payment', $next_payment);

        $this->design->assign('schedules', $schedules);
        $this->design->assign('payment_schedule', $payment_schedule);

        $loantype = $this->Loantypes->get_loantype($order->loan_type);
        $this->design->assign('loantype', $loantype);

        $loantypes = $this->GroupLoanTypes->get_loantypes_on($order->group_id);
        $this->design->assign('loantypes', $loantypes);

        $order = $this->orders->get_order($order_id);

        $filter['order_id'] = $order->order_id;

        $documents = $this->documents->get_documents($filter);

        $scans = $this->Scans->get_scans_by_order_id($order_id);
        $asp_restruct = 0;
        $need_confirm_restruct = 0;

        foreach ($documents as $document) {
            foreach ($scans as $scan) {
                if ($document->template == $scan->type)
                    $document->scan = $scan;
            }
            if (in_array($document->type, ['DOP_GRAFIK', 'DOP_SOGLASHENIE']) && empty($document->asp_id)) {

                if (empty($document->pre_asp_id)) {
                    $asp_restruct = 1;
                } else {
                    $asp_restruct = 0;
                    $need_confirm_restruct = 1;
                }
            }
        }

        $this->design->assign('need_confirm_restruct', $need_confirm_restruct);
        $this->design->assign('asp_restruct', $asp_restruct);
        $this->design->assign('documents', $documents);

        $settlement = $this->OrganisationSettlements->get_settlement($order->settlement_id);
        $this->design->assign('settlement', $settlement);

        $body = $this->design->fetch('offline/order.tpl');

        if ($this->request->get('ajax', 'integer')) {
            echo $body;
            exit;
        }

        return $body;
    }

    private function action_contact_status()
    {
        $contact_status = $this->request->post('contact_status', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $this->users->update_user($user_id, array('contact_status' => $contact_status));

        return array('success' => 1, 'contact_status' => $contact_status);
    }

    private
    function action_contactperson_status()
    {
        $contact_status = $this->request->post('contact_status', 'integer');
        $contactperson_id = $this->request->post('contactperson_id', 'integer');

        $this->contactpersons->update_contactperson($contactperson_id, array('contact_status' => $contact_status));

        return array('success' => 1, 'contact_status' => $contact_status);
    }

    private
    function action_workout()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $workout = $this->request->post('workout', 'integer');

        $this->orders->update_order($order_id, array('quality_workout' => $workout));

        return array('success' => 1, 'contact_status' => $contact_status);
    }

    private
    function confirm_contract_action()
    {
        $contract_id = $this->request->post('contract_id', 'integer');
        $code = $this->request->post('code', 'integer');
        $phone = $this->request->post('phone');

        if (!($contract = $this->contracts->get_contract($contract_id)))
            return array('error' => 'Договор не найден');

        if ($contract->status != 0)
            return array('error' => 'Договор не находится в статусе Новый!');

        $db_code = $this->sms->get_code($phone);
        if ($contract->accept_code != $code) {
            return array('error' => 'Код не совпадает');
        } else {
            $this->contracts->update_contract($contract_id, array(
                'status' => 1,
                'accept_code' => $code,
                'accept_date' => date('Y-m-d H:i:s'),
                'accept_ip' => $_SERVER['REMOTE_ADDR']
            ));

            $this->orders->update_order($contract->order_id, array(
                'status' => 4,
                'confirm_date' => date('Y-m-d H:i:s'),
            ));

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'confirm_order',
                'old_values' => serialize(array('status' => 3)),
                'new_values' => serialize(array('status' => 4)),
                'order_id' => $contract->order_id,
                'user_id' => $contract->user_id,
            ));

            return array('success' => 1, 'status' => 4, 'manager' => $this->manager->name);

        }

    }

    private
    function change_manager_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $manager_id = $this->request->post('manager_id', 'integer');

        if (!($order = $this->orders->get_order((int)$order_id)))
            return array('error' => 'Неизвестный ордер');

        if (!in_array($this->manager->role, array('admin', 'developer', 'big_user')))
            return array('error' => 'Не хватает прав для выполнения операции', 'manager_id' => $order->manager_id);

        $update = array(
            'manager_id' => $manager_id,
        );
        if (empty($manager_id))
            $update['status'] = 1;

        $this->orders->update_order($order_id, $update);

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'status_order',
            'old_values' => serialize(array('status' => $order->status, 'manager_id' => $order->manager_id)),
            'new_values' => serialize($update),
            'order_id' => $order_id,
            'user_id' => $order->user_id,
        ));

        return array('success' => 1, 'status' => 1, 'manager' => $this->manager->name);

    }

    /**
     * OrderController::accept_order_action()
     * Принятие ордера в работу менеджером
     *
     * @return array
     */
    private
    function accept_order_action()
    {
        $order_id = $this->request->post('order_id', 'integer');

        if (!($order = $this->orders->get_order((int)$order_id)))
            return array('error' => 'Неизвестный ордер');

        if (!empty($order->manager_id) && $order->manager_id != $this->manager->id && !in_array($this->manager->role, array('admin', 'developer')))
            return array('error' => 'Ордер уже принят другим пользователем', 'manager_id' => $order->manager_id);

        $update = array(
            'status' => 1,
            'manager_id' => $this->manager->id,
            'uid' => exec($this->config->root_dir . 'generic/uidgen'),
            'accept_date' => date('Y-m-d H:i:s'),
        );
        $this->orders->update_order($order_id, $update);

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'accept_order',
            'old_values' => serialize(array('status' => $order->status, 'manager_id' => $order->manager_id)),
            'new_values' => serialize($update),
            'order_id' => $order_id,
            'user_id' => $order->user_id,
        ));

        return array('success' => 1, 'status' => 1, 'manager' => $this->manager->name);
    }

    /**
     * OrderController::approve_order_action()
     * Одобрение заявки
     * @return array
     */
    private
    function approve_order_action()
    {
        $order_id = $this->request->post('order_id', 'integer');

        $order = $this->orders->get_order((int)$order_id);

        $loan = $this->Loantypes->get_loantype($order->loan_type);

        $query = $this->db->placehold("
        SELECT `type`
        FROM s_scans
        WHERE user_id = ?
        AND order_id = ?
        AND `type` != 'ndfl'
        ", (int)$order->user_id, (int)$order->order_id);

        $this->db->query($query);
        $scans = $this->db->results();

        $users_docs = $this->Documents->get_documents(['order_id' => $order_id]);

        if (empty($users_docs)) {
            echo json_encode(['error' => 'Не сформированы документы!']);
            exit;
        }

        if (!empty($order->sms)) {
            $count_scans_without_asp = 0;

            foreach ($scans as $scan) {
                foreach ($users_docs as $doc) {
                    if ($doc->template == $scan->type && in_array($scan->type, ['soglasie_rukred_rabotadatel.tpl', 'zayavlenie_zp_v_schet_pogasheniya_mrk.tpl']))
                        $count_scans_without_asp++;
                }
            }

            if ($count_scans_without_asp < 2) {
                echo json_encode(['error' => 'Проверьте сканы для форм 03.03 и 03.04']);
                exit;
            }
        }

        if (count($scans) < count($users_docs) && empty($order->sms)) {
            echo json_encode(['error' => 'Для одобрения заявки нужны все сканы либо пэп!']);
            exit;
        }

        if ($order->amount < $loan->min_amount && $order->amount > $loan->max_amount) {
            echo json_encode(['error' => 'Проверьте сумму займа!']);
            exit;
        }

        $update = array(
            'status' => 4,
            'manager_id' => $this->manager->id,
            'approve_date' => date('Y-m-d H:i:s'),
        );
        $old_values = array(
            'status' => $order->status,
            'manager_id' => $order->manager_id
        );

        $this->orders->update_order($order_id, $update);

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'order_status',
            'old_values' => serialize($old_values),
            'new_values' => serialize($update),
            'order_id' => $order_id,
            'user_id' => $order->user_id,
        ));

        $this->contracts->update_contract($order->contract_id, ['status' => 1]);
        $communication_theme = $this->CommunicationsThemes->get(12);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => 12,
                'company_id' => 3,
                'group_id' => 2,
                'order_id' => $order_id,
                'status' => 1
            ];

        $ticket_id = $this->Tickets->add_ticket($ticket);
        $message =
            [
                'message' => $communication_theme->text,
                'ticket_id' => $ticket_id,
                'manager_id' => $this->manager->id,
            ];
        $this->TicketMessages->add_message($message);

        $scoring_types = $this->scorings->get_types();
        foreach ($scoring_types as $scoring_type) {
            if ($scoring_type->name == 'okb') {
                $add_scoring = array(
                    'user_id' => $order->user_id,
                    'order_id' => $order->order_id,
                    'type' => $scoring_type->name,
                    'status' => 'new',
                    'start_date' => date('Y-m-d H:i:s'),
                );
                $this->scorings->add_scoring($add_scoring);
            }
        }

        $this->db->query("
        SELECT id
        FROM s_asp_codes
        WHERE order_id = ?
        ORDER BY id DESC
        LIMIT 1
        ", $order_id);
        $asp_id = $this->db->result('id');

        $this->documents->update_asp(['order_id' => $order_id, 'asp_id' => $asp_id]);

        try {
            $upload_scans = 0;

            if (count($scans) == count($users_docs))
                $upload_scans = 1;

            $this->YaDisk->upload_orders_files($order_id, $upload_scans);
        } catch (Exception $e) {

        }

        echo json_encode(['success' => 1]);
        exit;

    }

    /**
     * OrderController::delivery_order_action()
     *  Оплата ордера менеджером
     *
     * @return array
     */
    private
    function delivery_order_action()
    {
        $order_id = (int)$this->request->post('order_id', 'integer');

        $order = $this->orders->get_order($order_id);

        $resp = $this->best2pay->issuance($order_id);

        if (isset($resp['success']) && $resp['success'] == 1) {

            $payment_schedule = $this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);
            $payment_schedule = json_decode($payment_schedule->schedule, true);
            $date = date('Y-m-d');

            foreach ($payment_schedule as $payday => $payment) {
                if ($payday != 'result') {
                    $payday = date('Y-m-d', strtotime($payday));
                    if ($payday > $date) {
                        $next_payday = $date;
                        break;
                    }
                }
            }

            $contract =
                [
                    'loan_body_summ' => $order->amount,
                    'status' => 2,
                    'return_date' => $next_payday
                ];

            $this->contracts->update_contract($order->contract_id, $contract);
            $this->orders->update_order($order_id, ['status' => 5]);

            $this->operations->add_operation([
                'user_id' => $order->user_id,
                'contract_id' => $order->contract_id,
                'order_id' => $order->order_id,
                'type' => 'P2P',
                'amount' => $order->amount,
                'created' => date('Y-m-d H:i:s'),
                'loan_body_summ' => $order->amount,
                'loan_percents_summ' => 0,
                'loan_peni_summ' => 0,
            ]);

            $communication_theme = $this->CommunicationsThemes->get(17);


            $ticket = [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => 17,
                'company_id' => 2,
                'group_id' => $order->group_id,
                'order_id' => $order_id,
                'status' => 0
            ];

            $ticket_id = $this->Tickets->add_ticket($ticket);

            $message =
                [
                    'message' => $communication_theme->text,
                    'ticket_id' => $ticket_id,
                    'manager_id' => $this->manager->id,
                ];

            $this->TicketMessages->add_message($message);

            $this->design->assign('order', $order);
            $documents = $this->documents->get_documents(['order_id' => $order->order_id]);
            $docs_email = [];

            foreach ($documents as $document) {
                if (in_array($document->type, ['INDIVIDUALNIE_USLOVIA', 'GRAFIK_OBSL_MKR']))
                    $docs_email[$document->type] = $document->id;
            }

            $individ_encrypt = $this->config->back_url . '/online_docs/' . Encryption::encryption(rand(1, 9999999999) . ' ' . $docs_email['INDIVIDUALNIE_USLOVIA'] . ' ' . rand(1, 9999999999));
            $graphic_encrypt = $this->config->back_url . '/online_docs/' . Encryption::encryption(rand(1, 9999999999) . ' ' . $docs_email['GRAFIK_OBSL_MKR'] . ' ' . rand(1, 9999999999));

            $this->design->assign('individ_encrypt', $individ_encrypt);
            $this->design->assign('graphic_encrypt', $graphic_encrypt);
            $fetch = $this->design->fetch('email/approved.tpl');

            $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
            $mailResponse = $mailService->send(
                'rucred@ucase.live',
                $order->email,
                'RuCred | Ваш займ успешно выдан',
                'Поздравляем!',
                $fetch
            );

            return ['success' => 1];
        } else {
            return $resp;
        }

    }

    /**
     * OrderController::delivery_order_status_action()
     *  Проверка статуса выплаты ордера
     *
     * @return array
     */
    private
    function delivery_order_status_action()
    {
        $order_id = (int)$this->request->post('order_id', 'integer');
        $order = $this->orders->get_order($order_id);

        if (!$order) {
            return array('error' => 'Неизвестный ордер');
        }

        if (!empty($order->manager_id) && $order->manager_id !== $this->manager->id && !in_array($this->manager->role, array('admin', 'developer'))) {
            return array('error' => 'Не хватает прав для выполнения операции');
        }

        $best2pay_endpoint = $this->config->best2pay_endpoint;
        $action = "Order";
        $request_url = $best2pay_endpoint . $action;

        $best2pay_sector = (int)$this->config->best2pay_current_sector_id;

        $best2pay_reference = $order_id;

        $best2pay_password = $this->config->best2pay_sector3159_pass;

        $best2pay_signature = base64_encode(md5($best2pay_sector . $best2pay_reference . $best2pay_password));

        try {
            $ch = curl_init($request_url);
            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'sector' => $best2pay_sector,
                'reference' => $best2pay_reference,
                'signature' => $best2pay_signature,
            ], JSON_THROW_ON_ERROR));
            $best2pay_response = curl_exec($ch);
            curl_close($ch);
            $best2pay_response_xml = simplexml_load_string($best2pay_response);
            $best2pay_response_xml_name = $best2pay_response_xml->getName();
            if ($best2pay_response_xml_name === 'error') {
                return array('error' => $best2pay_response_xml->description);
            }
        } catch (Exception $e) {
            return array('error' => 1);
        }
    }

    private
    function autoretry_accept_action()
    {
        $order_id = $this->request->post('order_id', 'integer');

        if (!($order = $this->orders->get_order((int)$order_id)))
            return array('error' => 'Неизвестный ордер');

        if (!empty($order->manager_id) && $order->manager_id != $this->manager->id && !in_array($this->manager->role, array('admin', 'developer')))
            return array('error' => 'Не хватает прав для выполнения операции');

        if ($order->amount > 15000)
            return array('error' => 'Сумма займа должна быть не более 15000 руб!');

        if ($order->period != 14)
            return array('error' => 'Срок займа должен быть 14 дней!');

        $update = array(
            'status' => 2,
            'amount' => $order->autoretry_summ,
            'manager_id' => $this->manager->id
        );
        $old_values = array(
            'status' => $order->status,
            'amount' => $order->amount,
            'manager_id' => $order->manager_id
        );
        /*
                $this->orders->update_order($order_id, $update);

                $this->changelogs->add_changelog(array(
                    'manager_id' => $this->manager->id,
                    'created' => date('Y-m-d H:i:s'),
                    'type' => 'order_status',
                    'old_values' => serialize($old_values),
                    'new_values' => serialize($update),
                    'order_id' => $order_id,
                    'user_id' => $order->user_id,
                ));

                $accept_code = rand(1000, 9999);

                $new_contract = array(
                    'order_id' => $order_id,
                    'user_id' => $order->user_id,
                    'card_id' => $order->card_id,
                    'type' => 'base',
                    'amount' => $order->amount,
                    'period' => $order->period,
                    'create_date' => date('Y-m-d H:i:s'),
                    'status' => 0,
                    'base_percent' => $this->settings->loan_default_percent,
                    'charge_percent' => $this->settings->loan_charge_percent,
                    'peni_percent' => $this->settings->loan_peni,
                    'service_reason' => $order->service_reason,
                    'service_insurance' => $order->service_insurance,
                    'accept_code' => $accept_code,
                );
                $contract_id = $this->contracts->add_contract($new_contract);

                $this->orders->update_order($order_id, array('contract_id' => $contract_id));

                // отправялем смс
                $msg = 'Активируй займ '.($order->autoretry_summ*1).' в личном кабинете, код'.$accept_code.' nalichnoeplus.com/lk';
                $this->sms->send($order->phone_mobile, $msg);
        */
        return array('success' => 1, 'status' => 2);

    }

    private
    function reject_order_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $reason_id = $this->request->post('reason', 'integer');
        $status = $this->request->post('status', 'integer');

        if (!($order = $this->orders->get_order((int)$order_id)))
            return array('error' => 'Неизвестный ордер');

        $reason = $this->reasons->get_reason($reason_id);

        $update = array(
            'status' => $status,
            'manager_id' => $this->manager->id,
            'reject_reason' => $reason->client_name,
            'reason_id' => $reason_id,
            'reject_date' => date('Y-m-d H:i:s'),
        );
        $old_values = array(
            'status' => $order->status,
            'manager_id' => $order->manager_id,
            'reject_reason' => $order->reject_reason
        );

        if (!empty($order->manager_id) && $order->manager_id != $this->manager->id && !in_array($this->manager->role, array('admin', 'developer')))
            return array('error' => 'Не хватает прав для выполнения операции');

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order);echo '</pre><hr />';
        $this->orders->update_order($order_id, $update);

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'order_status',
            'old_values' => serialize($old_values),
            'new_values' => serialize($update),
            'order_id' => $order_id,
            'user_id' => $order->user_id,
        ));

        // отправляем письмо независимо от того сняли за причину отказа или нет
        $this->notify->send_reject_reason($order_id);

        // проверяем были ли уже списания за причину отказа, что бы не списать второй раз
        $reject_operations = $this->operations->get_operations(array(
            'type' => 'REJECT_REASON',
            'order_id' => $order->order_id
        ));

        // Снимаем за причину отказа
        if (empty($reject_operations)) {
            if (!empty($order->service_reason) && $status == 3) {

                $service_summ = $this->settings->reject_reason_cost * 100;

                $description = 'Услуга "Узнай причину отказа"';

                $response = $this->best2pay->recurrent($order->card_id, $service_summ, $description);
                //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(htmlspecialchars($response));echo '</pre><hr />';

                $xml = simplexml_load_string($response);
                $b2p_status = (string)$xml->state;

                if ($b2p_status == 'APPROVED') {
                    $transaction = $this->transactions->get_operation_transaction($xml->order_id, $xml->id);

                    $operation_id = $this->operations->add_operation(array(
                        'contract_id' => 0,
                        'user_id' => $order->user_id,
                        'order_id' => $order->order_id,
                        'type' => 'REJECT_REASON',
                        'amount' => $this->settings->reject_reason_cost,
                        'created' => date('Y-m-d H:i:s'),
                        'transaction_id' => $transaction->id,
                    ));

                    $operation = $this->operations->get_operation($operation_id);
                    $operation->transaction = $this->transactions->get_transaction($transaction->id);

                    $this->operations->update_operation($operation->id, array(
                        'sent_status' => 2,
                        'sent_date' => date('Y-m-d H:i:s')
                    ));

                    //Отправляем чек
                    $this->cloudkassir->send_reject_reason($order_id);


                    return true;
                    //echo __FILE__.' '.__LINE__.'<br /><pre>';echo(htmlspecialchars($recurring));echo $contract_id.'</pre><hr />';exit;

                } else {
                    return false;
                }
            }
        }

        return array('success' => 1, 'status' => $status);
    }

    private
    function status_action($status)
    {
        $order_id = $this->request->post('order_id', 'integer');

        if (!($order = $this->orders->get_order((int)$order_id)))
            return array('error' => 'Неизвестный ордер');

        $update = array(
            'status' => $status,
        );
        $old_values = array(
            'status' => $order->status,
        );

        if ($status == 1) {
            if (!empty($order->manager_id) && $order->manager_id != $this->manager->id && !in_array($this->manager->role, array('admin', 'developer')))
                return array('error' => 'Ордер уже принят другим пользователем', 'manager_id' => $order->manager_id);

            $update['manager_id'] = $this->manager->id;
            $old_values['manager_id'] = '';
        }

        if (!empty($order->manager_id) && $order->manager_id != $this->manager->id)
            return array('error' => 'Не хватает прав для выполнения операции');

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order);echo '</pre><hr />';
        $this->orders->update_order($order_id, $update);

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'order_status',
            'old_values' => serialize($old_values),
            'new_values' => serialize($update),
            'order_id' => $order_id,
            'user_id' => $order->user_id,
        ));

        return array('success' => 1, 'status' => $status);
    }

    private
    function action_cards()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');
        $card_id = $this->request->post('card_id', 'integer');

        $order = new StdClass();
        $order->order_id = $order_id;
        $order->user_id = $user_id;
        $order->card_id = $card_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);

        $card_error = array();

        if (empty($card_id))
            $card_error[] = 'empty_card';

        if (empty($card_error)) {
            $update = array(
                'card_id' => $card_id
            );

            $old_order = $this->orders->get_order($order_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_order->$key != $update[$key])
                    $old_values[$key] = $old_order->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'card',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
            ));

            $this->orders->update_order($order_id, $update);

        }
        $this->design->assign('card_error', $card_error);

        $cards = array();
        foreach ($this->cards->get_cards(array('user_id' => $order->user_id)) as $card)
            $cards[$card->id] = $card;
        $this->design->assign('cards', $cards);

    }

    private
    function action_amount()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');
        $amount = $this->request->post('amount', 'integer');
        $period = $this->request->post('period', 'integer');

        $order = new StdClass();
        $order->order_id = $order_id;
        $order->user_id = $user_id;
        $order->amount = $amount;
        $order->period = $period;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $amount_error = array();

        if (empty($amount))
            $amount_error[] = 'empty_amount';
        if (empty($period))
            $amount_error[] = 'empty_period';

        if ($isset_order->status > 2 && !in_array($this->manager->role, array('admin', 'developer'))) {
            $amount_error[] = 'Невозможно изменить сумму в этом статусе заявки';
            $order->amount = $isset_order->amount;
            $order->period = $isset_order->period;
        }

        $this->design->assign('order', $order);

        if (empty($amount_error)) {
            $update = array(
                'amount' => $amount,
                'period' => $period
            );

            $old_order = $this->orders->get_order($order_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_order->$key != $update[$key])
                    $old_values[$key] = $old_order->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'period_amount',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
            ));

            $this->orders->update_order($order_id, $update);

            if (!empty($old_order->contract_id)) {
                $this->contracts->update_contract($old_order->contract_id, array(
                    'amount' => $amount,
                    'period' => $period
                ));
            }
        }
        $this->design->assign('amount_error', $amount_error);
    }

    private
    function contactdata_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();

        $order->email = trim($this->request->post('email'));
        $order->birth = trim($this->request->post('birth'));
        $order->birth_place = trim($this->request->post('birth_place'));
        $order->passport_serial = trim($this->request->post('passport_serial'));
        $order->passport_date = trim($this->request->post('passport_date'));
        $order->subdivision_code = trim($this->request->post('subdivision_code'));
        $order->passport_issued = trim($this->request->post('passport_issued'));

        $order->social = trim($this->request->post('social'));

        $contactdata_error = array();

        if (empty($order->email))
            $personal_error[] = 'empty_email';
        if (empty($order->birth))
            $personal_error[] = 'empty_birth';
        if (empty($order->birth_place))
            $personal_error[] = 'empty_birth_place';
        if (empty($order->passport_serial))
            $personal_error[] = 'empty_passport_serial';
        if (empty($order->passport_date))
            $personal_error[] = 'empty_passport_date';
        if (empty($order->subdivision_code))
            $personal_error[] = 'empty_subdivision_code';
        if (empty($order->passport_issued))
            $personal_error[] = 'empty_passport_issued';
        if (empty($order->social))
            $personal_error[] = 'empty_socials';


        if (empty($contactdata_error)) {
            $update = array(
                'email' => $order->email,
                'birth' => $order->birth,
                'birth_place' => $order->birth_place,
                'passport_serial' => $order->passport_serial,
                'passport_date' => $order->passport_date,
                'subdivision_code' => $order->subdivision_code,
                'passport_issued' => $order->passport_issued,
                'social' => $order->social,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'contactdata',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);

            // редактирование в документах
            if (!empty($user_id)) {
                $documents = $this->documents->get_documents(array('user_id' => $user_id));
                foreach ($documents as $doc) {
                    if (isset($doc->params['email']))
                        $doc->params['email'] = $order->email;
                    if (isset($doc->params['birth']))
                        $doc->params['birth'] = $order->birth;
                    if (isset($doc->params['birth_place']))
                        $doc->params['birth_place'] = $order->birth_place;
                    if (isset($doc->params['passport_serial']))
                        $doc->params['passport_serial'] = $order->passport_serial;
                    if (isset($doc->params['passport_date']))
                        $doc->params['passport_date'] = $order->passport_date;
                    if (isset($doc->params['subdivision_code']))
                        $doc->params['subdivision_code'] = $order->subdivision_code;
                    if (isset($doc->params['passport_issued']))
                        $doc->params['passport_issued'] = $order->passport_issued;

                    $this->documents->update_document($doc->id, array('params' => $doc->params));
                }
            }
        }

        $this->design->assign('contactdata_error', $contactdata_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);

    }

    private
    function contacts_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->contact_person_name = trim($this->request->post('contact_person_name'));
        $order->contact_person_phone = trim($this->request->post('contact_person_phone'));
        $order->contact_person_relation = trim($this->request->post('contact_person_relation'));
        $order->contact_person2_name = trim($this->request->post('contact_person2_name'));
        $order->contact_person2_phone = trim($this->request->post('contact_person2_phone'));
        $order->contact_person2_relation = trim($this->request->post('contact_person2_relation'));

        $contacts_error = array();

        if (empty($order->contact_person_name))
            $contacts_error[] = 'empty_contact_person_name';
        if (empty($order->contact_person_phone))
            $contacts_error[] = 'empty_contact_person_phone';
        if (empty($order->contact_person2_name))
            $contacts_error[] = 'empty_contact_person2_name';
        if (empty($order->contact_person2_phone))
            $contacts_error[] = 'empty_contact_person2_phone';

        if (empty($contacts_error)) {
            $update = array(
                'contact_person_name' => $order->contact_person_name,
                'contact_person_phone' => $order->contact_person_phone,
                'contact_person_relation' => $order->contact_person_relation,
                'contact_person2_name' => $order->contact_person2_name,
                'contact_person2_phone' => $order->contact_person2_phone,
                'contact_person2_relation' => $order->contact_person2_relation,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'contacts',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('contacts_error', $contacts_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }

    private
    function fio_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->lastname = trim($this->request->post('lastname'));
        $order->firstname = trim($this->request->post('firstname'));
        $order->patronymic = trim($this->request->post('patronymic'));
        $order->phone_mobile = trim($this->request->post('phone_mobile'));

        $fio_error = array();

        if (empty($order->lastname))
            $contacts_error[] = 'empty_lastname';
        if (empty($order->firstname))
            $contacts_error[] = 'empty_firstname';
        if (empty($order->patronymic))
            $contacts_error[] = 'empty_patronymic';
        if (empty($order->phone_mobile))
            $contacts_error[] = 'empty_phone_mobile';

        if (empty($fio_error)) {
            $update = array(
                'lastname' => $order->lastname,
                'firstname' => $order->firstname,
                'patronymic' => $order->patronymic,
                'phone_mobile' => $order->phone_mobile,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'fio',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);

            // редактирование в документах
            if (!empty($user_id)) {
                $documents = $this->documents->get_documents(array('user_id' => $user_id));
                foreach ($documents as $doc) {
                    if (isset($doc->params['lastname']))
                        $doc->params['lastname'] = $order->lastname;
                    if (isset($doc->params['firstname']))
                        $doc->params['firstname'] = $order->firstname;
                    if (isset($doc->params['patronymic']))
                        $doc->params['patronymic'] = $order->patronymic;
                    if (isset($doc->params['fio']))
                        $doc->params['fio'] = $order->lastname . ' ' . $order->firstname . ' ' . $order->patronymic;
                    if (isset($doc->params['phone']))
                        $doc->params['phone'] = $order->phone_mobile;

                    $this->documents->update_document($doc->id, array('params' => $doc->params));
                }
            }

        }

        $this->design->assign('fio_error', $fio_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;
        $order->phone_mobile = $isset_order->phone_mobile;

        $this->design->assign('order', $order);
    }

    private
    function addresses_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->Regindex = trim($this->request->post('Regindex'));
        $order->Regregion = trim($this->request->post('Regregion'));
        $order->Regregion_shorttype = trim($this->request->post('Regregion_shorttype'));
        $order->Regdistrict = trim($this->request->post('Regdistrict'));
        $order->Regdistrict_shorttype = trim($this->request->post('Regdistrict_shorttype'));
        $order->Reglocality = trim($this->request->post('Reglocality'));
        $order->Reglocality_shorttype = trim($this->request->post('Reglocality_shorttype'));
        $order->Regcity = trim($this->request->post('Regcity'));
        $order->Regcity_shorttype = trim($this->request->post('Regcity_shorttype'));
        $order->Regstreet = trim($this->request->post('Regstreet'));
        $order->Regstreet_shorttype = trim($this->request->post('Regstreet_shorttype'));
        $order->Reghousing = trim($this->request->post('Reghousing'));
        $order->Regbuilding = trim($this->request->post('Regbuilding'));
        $order->Regroom = trim($this->request->post('Regroom'));

        $order->Faktindex = trim($this->request->post('Faktindex'));
        $order->Faktregion = trim($this->request->post('Faktregion'));
        $order->Faktregion_shorttype = trim($this->request->post('Faktregion_shorttype'));
        $order->Faktdistrict = trim($this->request->post('Faktdistrict'));
        $order->Faktdistrict_shorttype = trim($this->request->post('Faktdistrict_shorttype'));
        $order->Faktlocality = trim($this->request->post('Faktlocality'));
        $order->Faktlocality_shorttype = trim($this->request->post('Faktlocality_shorttype'));
        $order->Faktcity = trim($this->request->post('Faktcity'));
        $order->Faktcity_shorttype = trim($this->request->post('Faktcity_shorttype'));
        $order->Faktstreet = trim($this->request->post('Faktstreet'));
        $order->Faktstreet_shorttype = trim($this->request->post('Faktstreet_shorttype'));
        $order->Fakthousing = trim($this->request->post('Fakthousing'));
        $order->Faktbuilding = trim($this->request->post('Faktbuilding'));
        $order->Faktroom = trim($this->request->post('Faktroom'));

        $addresses_error = array();

        if (empty($order->Regregion))
            $addresses_error[] = 'empty_regregion';

        if (empty($order->Faktregion))
            $addresses_error[] = 'empty_faktregion';

        if (empty($addresses_error)) {
            $update = array(
                'Regregion' => $order->Regregion,
                'Regregion_shorttype' => $order->Regregion_shorttype,
                'Regcity' => $order->Regcity,
                'Regcity_shorttype' => $order->Regcity_shorttype,
                'Regdistrict' => $order->Regdistrict,
                'Regdistrict_shorttype' => $order->Regdistrict_shorttype,
                'Reglocality' => $order->Reglocality,
                'Reglocality_shorttype' => $order->Reglocality_shorttype,
                'Regstreet' => $order->Regstreet,
                'Regstreet_shorttype' => $order->Regstreet_shorttype,
                'Reghousing' => $order->Reghousing,
                'Regbuilding' => $order->Regbuilding,
                'Regroom' => $order->Regroom,
                'Regindex' => $order->Regindex,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'regaddress',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);

            $update = array(
                'Faktregion' => $order->Faktregion,
                'Faktregion_shorttype' => $order->Faktregion_shorttype,
                'Faktcity' => $order->Faktcity,
                'Faktcity_shorttype' => $order->Faktcity_shorttype,
                'Faktdistrict' => $order->Faktdistrict,
                'Faktdistrict_shorttype' => $order->Faktdistrict_shorttype,
                'Faktlocality' => $order->Faktlocality,
                'Faktlocality_shorttype' => $order->Faktlocality_shorttype,
                'Faktstreet' => $order->Faktstreet,
                'Faktstreet_shorttype' => $order->Faktstreet_shorttype,
                'Fakthousing' => $order->Fakthousing,
                'Faktbuilding' => $order->Faktbuilding,
                'Faktroom' => $order->Faktroom,
                'Faktindex' => $order->Faktindex,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'faktaddress',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);

        }

        $this->design->assign('addresses_error', $addresses_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);

    }

    private
    function work_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->workplace = trim($this->request->post('workplace'));
        $order->workaddress = trim($this->request->post('workaddress'));
        $order->workcomment = trim($this->request->post('workcomment'));
        $order->profession = trim($this->request->post('profession'));
        $order->workphone = trim($this->request->post('workphone'));
        $order->income = trim($this->request->post('income'));
        $order->expenses = trim($this->request->post('expenses'));
        $order->chief_name = trim($this->request->post('chief_name'));
        $order->chief_position = trim($this->request->post('chief_position'));
        $order->chief_phone = trim($this->request->post('chief_phone'));

        $work_error = array();

        if (empty($order->workplace))
            $work_error[] = 'empty_workplace';
        if (empty($order->profession))
            $work_error[] = 'empty_profession';
        if (empty($order->workphone))
            $work_error[] = 'empty_workphone';
        if (empty($order->income))
            $work_error[] = 'empty_income';
        if (empty($order->expenses))
            $work_error[] = 'empty_expenses';
        if (empty($order->chief_name))
            $work_error[] = 'empty_chief_name';
        if (empty($order->chief_phone))
            $work_error[] = 'empty_chief_phone';
        if (empty($order->chief_phone))
            $work_error[] = 'empty_chief_phone';


        if (empty($work_error)) {
            $update = array(
                'workplace' => $order->workplace,
                'workaddress' => $order->workaddress,
                'workcomment' => $order->workcomment,
                'profession' => $order->profession,
                'workphone' => $order->workphone,
                'income' => $order->income,
                'expenses' => $order->expenses,
                'chief_name' => $order->chief_name,
                'chief_position' => $order->chief_position,
                'chief_phone' => $order->chief_phone,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'workdata',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);

        }

        $this->design->assign('work_error', $work_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);

    }


    private
    function action_personal()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->lastname = trim($this->request->post('lastname'));
        $order->firstname = trim($this->request->post('firstname'));
        $order->patronymic = trim($this->request->post('patronymic'));
        $order->gender = trim($this->request->post('gender'));
        $order->birth = trim($this->request->post('birth'));
        $order->birth_place = trim($this->request->post('birth_place'));

        $personal_error = array();

        if (empty($order->lastname))
            $personal_error[] = 'empty_lastname';
        if (empty($order->firstname))
            $personal_error[] = 'empty_firstname';
        if (empty($order->patronymic))
            $personal_error[] = 'empty_patronymic';
        if (empty($order->gender))
            $personal_error[] = 'empty_gender';
        if (empty($order->birth))
            $personal_error[] = 'empty_birth';
        if (empty($order->birth_place))
            $personal_error[] = 'empty_birth_place';

        if (empty($personal_error)) {
            $update = array(
                'lastname' => $order->lastname,
                'firstname' => $order->firstname,
                'patronymic' => $order->patronymic,
                'gender' => $order->gender,
                'birth' => $order->birth,
                'birth_place' => $order->birth_place,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'personal',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('personal_error', $personal_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }

    private
    function action_passport()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->passport_serial = trim($this->request->post('passport_serial'));
        $order->passport_date = trim($this->request->post('passport_date'));
        $order->subdivision_code = trim($this->request->post('subdivision_code'));
        $order->passport_issued = trim($this->request->post('passport_issued'));

        $passport_error = array();

        if (empty($order->passport_serial))
            $passport_error[] = 'empty_passport_serial';
        if (empty($order->passport_date))
            $passport_error[] = 'empty_passport_date';
        if (empty($order->subdivision_code))
            $passport_error[] = 'empty_subdivision_code';
        if (empty($order->passport_issued))
            $passport_error[] = 'empty_passport_issued';

        if (empty($passport_error)) {
            $update = array(
                'passport_serial' => $order->passport_serial,
                'passport_date' => $order->passport_date,
                'subdivision_code' => $order->subdivision_code,
                'passport_issued' => $order->passport_issued
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'passport',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('passport_error', $passport_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }

    private
    function reg_address_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->Regindex = trim($this->request->post('Regindex'));
        $order->Regregion = trim($this->request->post('Regregion'));
        $order->Regregion_shorttype = trim($this->request->post('Regregion_shorttype'));
        $order->Regcity = trim($this->request->post('Regcity'));
        $order->Regcity_shorttype = trim($this->request->post('Regcity_shorttype'));
        $order->Regdistrict = trim($this->request->post('Regdistrict'));
        $order->Reglocality = trim($this->request->post('Reglocality'));
        $order->Regstreet = trim($this->request->post('Regstreet'));
        $order->Reghousing = trim($this->request->post('Reghousing'));
        $order->Regbuilding = trim($this->request->post('Regbuilding'));
        $order->Regroom = trim($this->request->post('Regroom'));

        $regaddress_error = array();

        if (empty($order->Regregion))
            $regaddress_error[] = 'empty_regregion';
        if (empty($order->Regcity))
            $regaddress_error[] = 'empty_regcity';
        if (empty($order->Regstreet))
            $regaddress_error[] = 'empty_regstreet';
        if (empty($order->Reghousing))
            $regaddress_error[] = 'empty_reghousing';

        if (empty($regaddress_error)) {
            $update = array(
                'Regindex' => $order->Regindex,
                'Regregion' => $order->Regregion,
                'Regregion_shorttype' => $order->Regregion_shorttype,
                'Regcity' => $order->Regcity,
                'Regcity_shorttype' => $order->Regcity_shorttype,
                'Regdistrict' => $order->Regdistrict,
                'Reglocality' => $order->Reglocality,
                'Regstreet' => $order->Regstreet,
                'Reghousing' => $order->Reghousing,
                'Regbuilding' => $order->Regbuilding,
                'Regroom' => $order->Regroom,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'regaddress',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('regaddress_error', $regaddress_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }

    private
    function fakt_address_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->Faktindex = trim($this->request->post('Faktindex'));
        $order->Faktregion = trim($this->request->post('Faktregion'));
        $order->Faktregion_shorttype = trim($this->request->post('Faktregion_shorttype'));
        $order->Faktcity = trim($this->request->post('Faktcity'));
        $order->Faktcity_shorttype = trim($this->request->post('Faktcity_shorttype'));
        $order->Faktdistrict = trim($this->request->post('Faktdistrict'));
        $order->Faktlocality = $this->request->post('Faktlocality');
        $order->Faktstreet = trim($this->request->post('Faktstreet'));
        $order->Fakthousing = trim($this->request->post('Fakthousing'));
        $order->Faktbuilding = trim($this->request->post('Faktbuilding'));
        $order->Faktroom = trim($this->request->post('Faktroom'));

        $faktaddress_error = array();

        if (empty($order->Faktregion))
            $faktaddress_error[] = 'empty_faktregion';
        if (empty($order->Faktcity))
            $faktaddress_error[] = 'empty_faktcity';
        if (empty($order->Faktstreet))
            $faktaddress_error[] = 'empty_faktstreet';
        if (empty($order->Fakthousing))
            $faktaddress_error[] = 'empty_fakthousing';

        if (empty($faktaddress_error)) {
            $update = array(
                'Faktindex' => $order->Faktindex,
                'Faktregion' => $order->Faktregion,
                'Faktregion_shorttype' => $order->Faktregion_shorttype,
                'Faktcity' => $order->Faktcity,
                'Faktcity_shorttype' => $order->Faktcity_shorttype,
                'Faktdistrict' => $order->Faktdistrict,
                'Faktlocality' => $order->Faktlocality,
                'Faktstreet' => $order->Faktstreet,
                'Fakthousing' => $order->Fakthousing,
                'Faktbuilding' => $order->Faktbuilding,
                'Faktroom' => $order->Faktroom,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'faktaddress',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('faktaddress_error', $faktaddress_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }


    private
    function workdata_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->work_scope = trim($this->request->post('work_scope'));
        $order->profession = trim($this->request->post('profession'));
        $order->work_phone = trim($this->request->post('work_phone'));
        $order->workplace = trim($this->request->post('workplace'));
        $order->workdirector_name = trim($this->request->post('workdirector_name'));
        $order->income_base = trim($this->request->post('income_base'));

        $workdata_error = array();

        if (empty($order->work_scope))
            $workaddress_error[] = 'empty_work_scope';
        if (empty($order->income_base))
            $workaddress_error[] = 'empty_income_base';

        if (empty($workdata_error)) {
            $update = array(
                'work_scope' => $order->work_scope,
                'profession' => $order->profession,
                'work_phone' => $order->work_phone,
                'workplace' => $order->workplace,
                'workdirector_name' => $order->workdirector_name,
                'income_base' => $order->income_base,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'workdata',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('workdata_error', $workdata_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }


    private
    function work_address_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->Workregion = trim($this->request->post('Workregion'));
        $order->Workcity = trim($this->request->post('Workcity'));
        $order->Workstreet = trim($this->request->post('Workstreet'));
        $order->Workhousing = trim($this->request->post('Workhousing'));
        $order->Workbuilding = trim($this->request->post('Workbuilding'));
        $order->Workroom = trim($this->request->post('Workroom'));

        $workaddress_error = array();

        if (empty($order->Workregion))
            $workaddress_error[] = 'empty_workregion';
        if (empty($order->Workcity))
            $workaddress_error[] = 'empty_workcity';
        if (empty($order->Workstreet))
            $workaddress_error[] = 'empty_workstreet';
        if (empty($order->Workhousing))
            $workaddress_error[] = 'empty_workhousing';

        if (empty($workaddress_error)) {
            $update = array(
                'Workregion' => $order->Workregion,
                'Workcity' => $order->Workcity,
                'Workstreet' => $order->Workstreet,
                'Workhousing' => $order->Workhousing,
                'Workbuilding' => $order->Workbuilding,
                'Workroom' => $order->Workroom,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'workaddress',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('workaddress_error', $workaddress_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }

    private
    function socials_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->social_fb = trim($this->request->post('social_fb'));
        $order->social_inst = trim($this->request->post('social_inst'));
        $order->social_vk = trim($this->request->post('social_vk'));
        $order->social_ok = trim($this->request->post('social_ok'));

        $socials_error = array();

        if (empty($socials_error)) {
            $update = array(
                'social_fb' => $order->social_fb,
                'social_inst' => $order->social_inst,
                'social_vk' => $order->social_vk,
                'social_ok' => $order->social_ok,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'socials',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('socials_error', $socials_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }

    private
    function action_images()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $statuses = $this->request->post('status');

        foreach ($statuses as $file_id => $status) {
            $update = array(
                'status' => $status,
                'id' => $file_id
            );
            $old_files = $this->users->get_file($file_id);
            $old_values = array();
            foreach ($update as $key => $val)
                $old_values[$key] = $old_files->$key;
            if ($old_values['status'] != $update['status']) {
                $this->changelogs->add_changelog(array(
                    'manager_id' => $this->manager->id,
                    'created' => date('Y-m-d H:i:s'),
                    'type' => 'images',
                    'old_values' => serialize($old_values),
                    'new_values' => serialize($update),
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'file_id' => $file_id,
                ));
            }

            $this->users->update_file($file_id, array('status' => $status));

            if ($status == 3) {
                $this->users->update_user($user_id, array('stage_files' => 0));
            } else {
                $have_reject = 0;
                if ($files = $this->users->get_files(array('user_id' => $user_id))) {
                    foreach ($have_reject as $item)
                        if ($item->status == 3)
                            $have_reject = 1;
                }
                if (empty($have_reject))
                    $this->users->update_user($user_id, array('stage_files' => 1));
                else
                    $this->users->update_user($user_id, array('stage_files' => 0));

            }


        }

        $order = new StdClass();
        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);

        $files = $this->users->get_files(array('user_id' => $user_id));

        //Отправляемв 1с
        $need_send = array();
        $files_dir = str_replace('https://', 'http://', $this->config->front_url . '/files/users/');
        foreach ($files as $f) {
            if ($f->sent_1c == 0 && $f->status == 2) {
                $need_send_item = new StdClass();
                $need_send_item->id = $f->id;
                $need_send_item->user_id = $f->user_id;
                $need_send_item->type = $f->type;
                $need_send_item->url = $files_dir . $f->name;

                $need_send[] = $need_send_item;
            }
        }

        $this->design->assign('files', $files);
    }

    private
    function action_services()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $user_id = $this->request->post('user_id', 'integer');

        $order = new StdClass();
        $order->service_sms = (int)$this->request->post('service_sms');
        $order->service_insurance = (int)$this->request->post('service_insurance');
        $order->service_reason = (int)$this->request->post('service_reason');

        $services_error = array();

        if (empty($services_error)) {
            $update = array(
                'service_sms' => $order->service_sms,
                'service_insurance' => $order->service_insurance,
                'service_reason' => $order->service_reason,
            );

            $old_user = $this->users->get_user($user_id);
            $old_values = array();
            foreach ($update as $key => $val)
                if ($old_user->$key != $update[$key])
                    $old_values[$key] = $old_user->$key;

            $log_update = array();
            foreach ($update as $k => $u)
                if (isset($old_values[$k]))
                    $log_update[$k] = $u;

            $this->changelogs->add_changelog(array(
                'manager_id' => $this->manager->id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'services',
                'old_values' => serialize($old_values),
                'new_values' => serialize($log_update),
                'order_id' => $order_id,
                'user_id' => $user_id,
            ));

            $this->users->update_user($user_id, $update);
        }

        $this->design->assign('services_error', $services_error);

        $order->order_id = $order_id;
        $order->user_id = $user_id;

        $isset_order = $this->orders->get_order((int)$order_id);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;

        $this->design->assign('order', $order);
    }

    private
    function action_add_comment()
    {
        $user_id = $this->request->post('user_id', 'integer');
        $contactperson_id = $this->request->post('contactperson_id', 'integer');
        $order_id = $this->request->post('order_id', 'integer');
        $text = $this->request->post('text');
        $official = $this->request->post('official', 'integer');
        $organization = $this->request->post('organization', 'string');

        if (empty($text)) {
            $this->json_output(array('error' => 'Напишите комментарий!'));
        } else {


            $comment = array(
                'manager_id' => $this->manager->id,
                'user_id' => $user_id,
                'contactperson_id' => $contactperson_id,
                'order_id' => $order_id,
                'text' => $text,
                'created' => date('Y-m-d H:i:s'),
                'official' => $official,
                'organization' => $organization,
            );

            if ($comment_id = $this->comments->add_comment($comment)) {
                $this->json_output(array(
                    'success' => 1,
                    'created' => date('d.m.Y H:i:s'),
                    'text' => $text,
                    'manager_name' => $this->manager->name,
                ));
            } else {
                $this->json_output(array('error' => 'Не удалось добавить!'));
            }
        }
    }

    private
    function action_close_contract()
    {
        $user_id = $this->request->post('user_id', 'integer');
        $order_id = $this->request->post('order_id', 'integer');
        $comment = $this->request->post('comment');
        $close_date = $this->request->post('close_date');

        if (empty($comment)) {
            $this->json_output(array('error' => 'Напишите комментарий к закрытию!'));
        } elseif (empty($close_date)) {
            $this->json_output(array('error' => 'Укажите дату закрытия!'));
        } else {
            if ($order = $this->orders->get_order($order_id)) {
                if ($contract = $this->contracts->get_contract($order->contract_id)) {
                    $comment = array(
                        'manager_id' => $this->manager->id,
                        'user_id' => $user_id,
                        'contactperson_id' => 0,
                        'order_id' => $order_id,
                        'text' => 'Закрыт в CRM. ' . $comment,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    if ($comment_id = $this->comments->add_comment($comment)) {
                        $this->orders->update_order($order_id, array('status' => 7));

                        $this->contracts->update_contract($contract->id, array(
                            'status' => 3,
                            'close_date' => date('Y-m-d H:i:s', strtotime($close_date)),
                            'loan_body_summ' => 0,
                            'loan_percents_summ' => 0,
                            'loan_charge_summ' => 0,
                            'loan_peni_summ' => 0,
                            'collection_status' => 0,
                            'collection_manager_id' => 0,
                        ));

                        $this->json_output(array(
                            'success' => 1,
                            'created' => date('d.m.Y H:i:s'),
                            'manager_name' => $this->manager->name,
                        ));
                    } else {
                        $this->json_output(array('error' => 'Не удалось добавить комментарий!'));
                    }
                } else {
                    $this->json_output(array('error' => 'Договор не найден!'));
                }
            } else {
                $this->json_output(array('error' => 'Заявка не найдена!'));
            }

        }
    }

    public
    function action_repay()
    {
        $contract_id = $this->request->post('contract_id', 'integer');

        if (!in_array('repay_button', $this->manager->permissions))
            $this->json_output(array('error' => 'Не хватает прав!'));

        if ($contract = $this->contracts->get_contract($contract_id)) {
            if ($order = $this->orders->get_order($contract->order_id)) {
                if ($order->status != 6 || $contract->status != 6) {
                    $this->json_output(array('error' => 'Невозможно выполнить!'));
                } else {
                    $this->contracts->update_contract($contract->id, array('status' => 1));
                    $this->orders->update_order($contract->order_id, array('status' => 4));

                    $this->changelogs->add_changelog(array(
                        'manager_id' => $this->manager->id,
                        'created' => date('Y-m-d H:i:s'),
                        'type' => 'status',
                        'old_values' => serialize(array('status' => 6)),
                        'new_values' => serialize(array('status' => 4)),
                        'order_id' => $contract->order_id,
                        'user_id' => $contract->user_id,
                    ));


                    $this->json_output(array(
                        'success' => 1,
                        'created' => date('d.m.Y H:i:s'),
                        'text' => 'Статус договора изменен',
                        'manager_name' => $this->manager->name,
                    ));
                }

            } else {
                $this->json_output(array('error' => 'Заявка не найдена!'));
            }
        } else {
            $this->json_output(array('error' => 'Договор не найден!'));
        }
    }

    private
    function send_sms_action()
    {
        $yuk = $this->request->post('yuk', 'integer');
        $user_id = $this->request->post('user_id', 'integer');
        $order_id = $this->request->post('order_id', 'integer');
        $template_id = $this->request->post('template_id', 'integer');

        $user = $this->users->get_user((int)$user_id);

        $template = $this->sms->get_template($template_id);

        if (!empty($order_id)) {
            $order = $this->orders->get_order($order_id);
            if (!empty($order->contract_id)) {
                $code = $this->helpers->c2o_encode($order->contract_id);
                $payment_link = $this->config->front_url . '/p/' . $code;
                $template->template = str_replace('{$payment_link}', $payment_link, $template->template);
            }
        }

        $resp = $this->sms->send(
            $user->phone_mobile,
            $template->template
        );

        $sms_message_id = $this->sms->add_message(array(
            'user_id' => $user->id,
            'order_id' => $order_id,
            'phone' => $user->phone_mobile,
            'message' => $template->template,
            'created' => date('Y-m-d H:i:s'),
        ));

        $this->communications->add_communication(array(
            'user_id' => $user->id,
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'sms',
            'content' => $template->template,
            'outer_id' => $sms_message_id,
            'from_number' => $this->sms->get_originator($yuk),
            'to_number' => $user->phone_mobile,
            'yuk' => $yuk,
            'result' => serialize($resp),
        ));

        $this->comments->add_comment(array(
            'user_id' => $user->id,
            'order_id' => $order_id,
            'manager_id' => $this->manager->id,
            'text' => 'Клиенту отправлено смс с текстом: ' . $template->template,
            'created' => date('Y-m-d H:i:s'),
            'organization' => empty($yuk) ? 'mkk' : 'yuk',
            'auto' => 1
        ));

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'send_sms',
            'old_values' => array(),
            'new_values' => array($template->template),
            'user_id' => $user->id,
            'order_id' => $order_id,
        ));
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
        $this->json_output(array('success' => true));
    }

    private
    function action_inn_change()
    {
        $user_id = (int)$this->request->post('user_id');
        $inn_number = $this->request->post('inn_number');

        $this->users->update_user($user_id, ['inn' => $inn_number]);
    }

    private
    function action_snils_change()
    {
        $user_id = (int)$this->request->post('user_id');
        $snils_number = $this->request->post('snils_number');

        $this->users->update_user($user_id, ['snils' => $snils_number]);
    }

    private
    function action_cors_change()
    {
        $user_id = (int)$this->request->post('user_id');

        $requisite = $this->request->post('requisite');
        $this->requisites->update_requisite($requisite['id'], $requisite);
    }

    private
    function action_edit_schedule()
    {

        $date = $this->request->post('date');
        $schedule_id = $this->request->post('schedule_id');
        $pay_sum = $this->request->post('pay_sum');
        $loan_percents_pay = $this->request->post('loan_percents_pay');
        $loan_body_pay = mb_convert_encoding($this->request->post('loan_body_pay'), 'UTF-8');
        $comission_pay = $this->request->post('comission_pay');
        $rest_pay = $this->request->post('rest_pay');
        $order_id = $this->request->post('order_id');
        $order = $this->orders->get_order($order_id);

        $results['result'] = $this->request->post('result');

        $payment_schedule = array_replace_recursive($date, $pay_sum, $loan_percents_pay, $loan_body_pay, $comission_pay, $rest_pay);

        foreach ($payment_schedule as $date => $payment) {
            $payment_schedule[$payment['date']] = array_slice($payment, 1);
            $payment_schedule[$payment['date']]['pay_sum'] = str_replace([" ", " ", ","], ['', '', '.'], $payment['pay_sum']);
            $payment_schedule[$payment['date']]['loan_percents_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $payment['loan_percents_pay']);
            $payment_schedule[$payment['date']]['loan_body_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $payment['loan_body_pay']);
            $payment_schedule[$payment['date']]['rest_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $payment['rest_pay']);
            unset($payment_schedule[$date]);
        }

        foreach ($results as $key => $result) {
            $results[$key]['all_sum_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $result['all_sum_pay']);
            $results[$key]['all_loan_percents_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $result['all_loan_percents_pay']);
            $results[$key]['all_loan_body_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $result['all_loan_body_pay']);
            $results[$key]['all_comission_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $result['all_comission_pay']);
            $results[$key]['all_rest_pay_sum'] = str_replace([" ", " ", ","], ['', '', '.'], $result['all_rest_pay_sum']);
        }

        $dates[0] = date('d.m.Y', strtotime($order->probably_start_date));
        $payments[0] = -$order->amount;

        foreach ($payment_schedule as $date => $pay) {
            $payments[] = (float)$pay['pay_sum'];
            $dates[] = date('d.m.Y', strtotime($date));
        }

        $payment_schedule = array_merge($payment_schedule, $results);

        foreach ($dates as $date) {

            $date = new DateTime(date('Y-m-d H:i:s', strtotime($date)));

            $new_dates[] = mktime(
                $date->format('H'),
                $date->format('i'),
                $date->format('s'),
                $date->format('m'),
                $date->format('d'),
                $date->format('Y')
            );
        }

        $xirr = round($this->Financial->XIRR($payments, $new_dates) * 100, 3);
        $xirr /= 100;

        $psk = round(((pow((1 + $xirr), (1 / 12)) - 1) * 12) * 100, 3);

        $update =
            [
                'psk' => $psk,
                'schedule' => json_encode($payment_schedule)
            ];

        $this->PaymentsSchedules->update($schedule_id, $update);
        exit;
    }

    private
    function action_change_photo_status()
    {
        $status = $this->request->post('status', 'integer');
        $file_id = $this->request->post('file_id');

        $type = 'document';

        switch ($status):

            case 2:
                $type = 'Паспорт: разворот';
                break;

            case 3:
                $type = 'Паспорт: регистрация';
                break;

            case 4:
                $type = 'Селфи с паспортом';
                break;

        endswitch;

        $query = $this->db->placehold("
        UPDATE s_files
        SET `type` = ?
        WHERE id = ?
        ", (string)$type, (int)$file_id);

        $this->db->query($query);
    }

    private
    function action_accept_by_employer()
    {
        $order_id = (int)$this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $this->orders->update_order($order_id, ['status' => 14]);
        $this->Tickets->close_neworder_ticket($order_id);
        $communication_theme = $this->CommunicationsThemes->get(11);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => 11,
                'company_id' => $order->company_id,
                'group_id' => 2,
                'order_id' => $order_id,
                'status' => 0
            ];

        $this->Tickets->add_ticket($ticket);
        exit;
    }

    private
    function action_reject_by_employer()
    {
        $order_id = (int)$this->request->post('order_id');
        $this->orders->update_order($order_id, ['status' => 15]);
        exit;
    }

    private
    function action_edit_personal_number()
    {
        $user_id = (int)$this->request->post('user_id');
        $order_id = (int)$this->request->post('order_id');
        $number = (int)$this->request->post('number');

        $check = $this->users->check_busy_number($number);

        if ($check && $check != 0) {
            echo 'error';
            exit;
        } else {

            $query = $this->db->placehold("
            SELECT uid
            FROM s_orders
            WHERE id = $order_id
            ");

            $this->db->query($query);
            $uid = $this->db->result('uid');

            $uid = explode(' ', $uid);
            $uid[3] = (string)$number;
            $uid = implode(' ', $uid);

            $this->users->update_user($user_id, ['personal_number' => $number]);
            $this->orders->update_order($order_id, ['uid' => $uid]);
            exit;
        }
    }

    private
    function action_change_loan_settings()
    {
        $order_id = (int)$this->request->post('order_id');
        $amount = $this->request->post('amount');
        $loan_tarif = $this->request->post('loan_tarif');
        $probably_start_date = $this->request->post('probably_start_date');
        $loantype = $this->Loantypes->get_loantype((int)$loan_tarif);
        $order = $this->orders->get_order($order_id);

        if (empty($order->branche_id)) {
            $branches = $this->Branches->get_branches(['group_id' => $order->group_id]);

            foreach ($branches as $branch) {
                if ($branch->number == '00') {
                    $first_pay_day = $branch->payday;
                    $user['branche_id'] = $branch->id;
                }
            }
        } else {
            $branch = $this->Branches->get_branch($order->branche_id);
            $first_pay_day = $branch->payday;
        }

        $probably_return_date = new DateTime(date('Y-m-' . $first_pay_day, strtotime($probably_start_date . '+' . $loantype->max_period . 'month')));
        $probably_return_date = $this->check_pay_date($probably_return_date);

        if ($amount < $loantype->min_amount || $amount > $loantype->max_amount) {
            echo json_encode(['error' => 'Проверьте сумму займа']);
            exit;
        }

        $order =
            [
                'amount' => $amount,
                'loan_type' => (int)$loan_tarif,
                'probably_start_date' => date('Y-m-d', strtotime($probably_start_date)),
                'probably_return_date' => $probably_return_date->format('Y-m-d')
            ];

        $this->orders->update_order($order_id, $order);
        $this->action_reform_schedule($order_id);
        echo json_encode(['success' => 1]);
        exit;
    }

    private
    function action_reform_schedule($order_id)
    {
        $order = $this->orders->get_order($order_id);
        $order = (array)$order;
        $loan = $this->Loantypes->get_loantype($order['loan_type']);
        $user = $this->users->get_user($order['user_id']);
        $user = (array)$user;

        if (empty($user['branche_id'])) {
            $branches = $this->Branches->get_branches(['company_id' => $user['company_id']]);

            foreach ($branches as $branch) {
                if ($branch->number == '00')
                    $first_pay_day = $branch->payday;
            }
        } else {
            $branch = $this->Branches->get_branch($user['branche_id']);
            $first_pay_day = $branch->payday;
        }

        $rest_sum = $order['amount'];
        $start_date = date('Y-m-d', strtotime($order['probably_start_date']));
        $end_date = $this->check_date($start_date, $order['loan_type']);
        $end_date = new DateTime(date('Y-m-d', strtotime($end_date)));
        $issuance_date = new DateTime(date('Y-m-d', strtotime($start_date)));
        $paydate = new DateTime(date('Y-m-' . "$first_pay_day", strtotime($start_date)));

        $percent_per_month = (($order['percent'] / 100) * 365) / 12;
        $percent_per_month = round($percent_per_month, 7);
        $annoouitet_pay = $order['amount'] * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
        $annoouitet_pay = round($annoouitet_pay, '2');

        if (date('d', strtotime($start_date)) < $first_pay_day) {
            if ($issuance_date > $start_date && date_diff($paydate, $issuance_date)->days < $loan->free_period) {
                $plus_loan_percents = round(($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $issuance_date)->days, 2);
                $sum_pay = $annoouitet_pay + $plus_loan_percents;
                $loan_percents_pay = round(($rest_sum * $percent_per_month) + $plus_loan_percents, 2);
                $body_pay = $sum_pay - $loan_percents_pay;
                $paydate->add(new DateInterval('P1M'));
                $paydate = $this->check_pay_date($paydate);
            } else {
                $sum_pay = ($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $issuance_date)->days;
                $loan_percents_pay = $sum_pay;
                $body_pay = 0;
            }

            $payment_schedule[$paydate->format('d.m.Y')] =
                [
                    'pay_sum' => $sum_pay,
                    'loan_percents_pay' => $loan_percents_pay,
                    'loan_body_pay' => $body_pay,
                    'comission_pay' => 0.00,
                    'rest_pay' => $rest_sum -= $body_pay
                ];
            $paydate->add(new DateInterval('P1M'));
        } else {
            $issuance_date = new DateTime(date('Y-m-d', strtotime($start_date)));
            $first_pay = new DateTime(date('Y-m-' . $first_pay_day, strtotime($start_date . '+1 month')));
            $count_days_this_month = date('t', strtotime($issuance_date->format('Y-m-d')));
            $paydate = $this->check_pay_date($first_pay);

            if (date_diff($first_pay, $issuance_date)->days <= $loan->min_period) {
                $sum_pay = ($order['percent'] / 100) * $order['amount'] * date_diff($first_pay, $issuance_date)->days;
                $percents_pay = $sum_pay;
                $body_pay = 0.00;
            }
            if (date_diff($first_pay, $issuance_date)->days > $loan->min_period && date_diff($first_pay, $issuance_date)->days < $count_days_this_month) {
                $minus_percents = ($order['percent'] / 100) * $order['amount'] * ($count_days_this_month - date_diff($first_pay, $issuance_date)->days);
                $sum_pay = $annoouitet_pay - round($minus_percents, 2);
                $percents_pay = ($rest_sum * $percent_per_month) - round($minus_percents, 2);
                $body_pay = $sum_pay - $percents_pay;
            }
            if (date_diff($first_pay, $issuance_date)->days >= $count_days_this_month) {
                $sum_pay = $annoouitet_pay;
                $percents_pay = $rest_sum * $percent_per_month;
                $body_pay = $sum_pay - $percents_pay;
            }

            $payment_schedule[$paydate->format('d.m.Y')] =
                [
                    'pay_sum' => $sum_pay,
                    'loan_percents_pay' => $percents_pay,
                    'loan_body_pay' => ($body_pay) ? $body_pay : 0,
                    'comission_pay' => 0.00,
                    'rest_pay' => $rest_sum -= $body_pay
                ];

            $paydate->add(new DateInterval('P1M'));
        }

        if ($rest_sum !== 0) {
            $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);
            $interval = new DateInterval('P1M');
            $lastdate = clone $end_date;
            $end_date->setTime(24, 0, 1);
            $daterange = new DatePeriod($paydate, $interval, $end_date);

            foreach ($daterange as $date) {
                $date = $this->check_pay_date($date);

                if ($date->format('m') == $end_date->format('m')) {
                    $loan_body_pay = $rest_sum;
                    $loan_percents_pay = $annoouitet_pay - $loan_body_pay;
                    $rest_sum = 0.00;
                } else {
                    $loan_percents_pay = round($rest_sum * $percent_per_month, 2);
                    $loan_body_pay = round($annoouitet_pay - $loan_percents_pay, 2);
                    $rest_sum = round($rest_sum - $loan_body_pay, 2);
                }

                $payment_schedule[$date->format('d.m.Y')] =
                    [
                        'pay_sum' => $annoouitet_pay,
                        'loan_percents_pay' => $loan_percents_pay,
                        'loan_body_pay' => $loan_body_pay,
                        'comission_pay' => 0.00,
                        'rest_pay' => $rest_sum
                    ];
            }
        }

        $payment_schedule['result'] =
            [
                'all_sum_pay' => 0.00,
                'all_loan_percents_pay' => 0.00,
                'all_loan_body_pay' => 0.00,
                'all_comission_pay' => 0.00,
                'all_rest_pay_sum' => 0.00
            ];

        $dates[0] = $start_date;
        $payments[0] = -$order['amount'];

        foreach ($payment_schedule as $date => $pay) {
            if ($date != 'result') {
                $payments[] = round($pay['pay_sum'], '2');
                $dates[] = date('d.m.Y', strtotime($date));
                $payment_schedule['result']['all_sum_pay'] += round($pay['pay_sum'], '2');
                $payment_schedule['result']['all_loan_percents_pay'] += round($pay['loan_percents_pay'], '2');
                $payment_schedule['result']['all_loan_body_pay'] += round($pay['loan_body_pay'], 2);
                $payment_schedule['result']['all_comission_pay'] += round($pay['comission_pay'], '2');
                $payment_schedule['result']['all_rest_pay_sum'] = 0.00;
            }
        }

        foreach ($dates as $date) {
            $date = new DateTime(date('Y-m-d H:i:s', strtotime($date)));

            $new_dates[] = mktime(
                $date->format('H'),
                $date->format('i'),
                $date->format('s'),
                $date->format('m'),
                $date->format('d'),
                $date->format('Y')
            );
        }

        $xirr = round($this->Financial->XIRR($payments, $new_dates) * 100, 3);
        $xirr /= 100;

        $psk = round(((pow((1 + $xirr), (1 / 12)) - 1) * 12) * 100, 3);

        $schedule = json_encode($payment_schedule);

        $actual_schedule = $this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);
        $this->PaymentsSchedules->update($actual_schedule->id, ['psk' => $psk, 'schedule' => $schedule]);
    }

    private
    function check_pay_date($date)
    {

        for ($i = 0; $i <= 15; $i++) {

            $check_date = $this->WeekendCalendar->check_date($date->format('Y-m-d'));

            if ($check_date == null) {
                break;
            } else {
                $date->sub(new DateInterval('P1D'));
            }
        }

        return $date;
    }

    private
    function action_delete_order()
    {

        $order_id = $this->request->post('order_id');

        $this->orders->update_order($order_id, ['status' => 16]);
    }

    private
    function action_change_employer_info()
    {
        $order_id = $this->request->post('order_id');
        $group_id = $this->request->post('group');
        $company_id = $this->request->post('company');
        $branch_id = $this->request->post('branch');

        $order =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'branche_id' => $branch_id
            ];

        $this->orders->update_order($order_id, $order);
        exit;
    }

    private
    function check_date($start_date, $loan_id)
    {
        $loan = $this->Loantypes->get_loantype($loan_id);

        $start_date = date('Y-m-d', strtotime($start_date));
        $first_pay = new DateTime(date('Y-m-10', strtotime($start_date)));
        $end_date = date('Y-m-10', strtotime($start_date . '+' . $loan->max_period . 'month'));

        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);

        if ($start_date > $first_pay) {
            $first_pay->add(new DateInterval('P1M'));
        }

        $first_pay = $this->check_pay_date($first_pay);

        if (date_diff($first_pay, $start_date)->days < 20 && $first_pay->format('m') != $start_date->format('m')) {
            $end_date->add(new DateInterval('P1M'));
        }


        for ($i = 0; $i <= 15; $i++) {
            $check_date = $this->WeekendCalendar->check_date($end_date->format('Y-m-d'));

            if ($check_date == null) {
                break;
            } else {
                $end_date->sub(new DateInterval('P1D'));
            }
        }

        return $end_date->format('d.m.Y');
    }

    private function action_restruct_term()
    {
        $order_id = $this->request->post('order_id');
        $new_term = $this->request->post('new_term');
        $pay_date = date('Y-m-d', strtotime($this->request->post('pay_date')));

        $payment_schedule = $this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);
        $payment_schedule = json_decode($payment_schedule->schedule, true);

        array_shift($payment_schedule);

        uksort($payment_schedule,
            function ($a, $b) {

                if ($a == $b)
                    return 0;

                return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
            });

        $i = 1;

        foreach ($payment_schedule as $date => $schedule) {
            $date = date('Y-m-d', strtotime($date));

            if ($pay_date < $date) {
                break;
            }
            $i++;
        }

        $term_diff = ($i + $new_term) - count($payment_schedule);

        if ($term_diff < 0) {
            mb_internal_encoding("UTF-8");
            $term_diff = mb_substr($term_diff, 1);
            $term_diff = 'Минус ' . $term_diff . ' мес. к старому графику';
        } else
            $term_diff = 'Плюс ' . $term_diff . ' мес. к старому графику';


        echo $term_diff;
        exit;
    }

    private
    function action_do_restruct()
    {
        $order_id = $this->request->post('order_id');
        $new_term = $this->request->post('new_term');
        $comment = $this->request->post('comment');
        $pay_amount = $this->request->post('pay_amount');
        $pay_date = date('d.m.Y', strtotime($this->request->post('pay_date')));
        $order = $this->orders->get_order($order_id);
        $order->new_term = $new_term;

        $payment_schedule = $this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);
        $payment_schedule = json_decode($payment_schedule->schedule, true);

        array_shift($payment_schedule);

        uksort($payment_schedule,
            function ($a, $b) {

                if ($a == $b)
                    return 0;

                return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
            });

        $new_shedule = array();

        $i = 0;
        $od_sum = 0;
        $new_loan = $order->amount;
        $percent_pay = 0.00;
        $body_pay = 0.00;
        $comission_pay = 0.00;

        foreach ($payment_schedule as $date => $schedule) {

            $date = date('d.m.Y', strtotime($date));
            if ($pay_date < $date) {
                if ($pay_amount < $schedule['pay_sum']) {
                    if ($pay_amount >= $schedule['loan_percents_pay']) {
                        $percent_pay = $schedule['loan_percents_pay'];
                        $pay_amount -= $percent_pay;

                        if ($pay_amount > 0) {
                            $body_pay = $pay_amount;
                            $new_loan -= $pay_amount;
                        }

                    } else {
                        $percent_pay = $pay_amount;
                        $plus_percents = round($schedule['loan_percents_pay'] - $pay_amount, 2);
                    }
                }

                if ($pay_amount > $schedule['pay_sum']) {
                    $percent_pay = $schedule['loan_percents_pay'];
                    $pay_amount -= $schedule['loan_percents_pay'];
                    $body_pay = $pay_amount;
                    $new_loan = $new_loan - ($pay_amount - $schedule['loan_body_pay']);
                }
                if ($pay_amount == 0) {
                    $new_loan += $pay_amount;
                }
                if ($pay_amount == $schedule['pay_sum']) {
                    $pay_amount = $schedule['pay_sum'];
                    $body_pay = $schedule['loan_body_pay'];
                    $percent_pay = $schedule['loan_percents_pay'];
                    $new_loan -= $schedule['loan_body_pay'];
                }

                $new_shedule[$date] =
                    [
                        'pay_sum' => $body_pay + $percent_pay,
                        'loan_body_pay' => $body_pay,
                        'loan_percents_pay' => $percent_pay,
                        'comission_pay' => $comission_pay,
                        'rest_pay' => $new_loan - $body_pay
                    ];

                $last_date = $date;
                break;
            }

            $new_shedule[$date] = $schedule;
            $new_loan -= round($schedule['loan_body_pay'], 2);

            $i++;
        }

        $user = (array)$this->users->get_user($order->user_id);

        if (empty($user['branche_id'])) {
            $branches = $this->Branches->get_branches(['company_id' => $user['company_id']]);

            foreach ($branches as $branch) {
                if ($branch->number == '00')
                    $first_pay_day = $branch->payday;
            }
        } else {
            $branch = $this->Branches->get_branch($user['branche_id']);
            $first_pay_day = $branch->payday;
        }

        $start_date = date('Y-m-' . $first_pay_day, strtotime($last_date . '+1 month'));
        $end_date = date('Y-m-' . $first_pay_day, strtotime($start_date . "+$new_term month"));
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);
        $end_date = $this->check_pay_date($end_date);
        $last_date = clone $end_date;
        $last_date->sub(new DateInterval('P1M'));
        $last_date = $this->check_pay_date($last_date);

        $interval = new DateInterval('P1M');
        $daterange = new DatePeriod($start_date, $interval, $end_date);
        $rest_sum = $new_loan;

        foreach ($daterange as $date) {
            $percent_per_month = (($order->percent / 100) * 365) / 12;
            $percent_per_month = round($percent_per_month, 7);
            $annoouitet_pay = $new_loan * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$new_term)));
            $annoouitet_pay = round($annoouitet_pay, '2');

            $date = $this->check_pay_date($date);

            if ($last_date->format('m') == $date->format('m')) {
                $loan_body_pay = $rest_sum;
                $loan_percents_pay = $annoouitet_pay - $loan_body_pay;
                $rest_sum = 0.00;
            } else {
                if (isset($plus_percents)) {
                    $loan_percents_pay = round($rest_sum * $percent_per_month, 2);
                    $loan_body_pay = round($annoouitet_pay - $loan_percents_pay, 2);
                    $loan_percents_pay += $plus_percents;
                    $annoouitet_pay += $plus_percents;
                    $rest_sum = round($rest_sum - $loan_body_pay, 2);
                    unset($plus_percents);
                } else {
                    $loan_percents_pay = round($rest_sum * $percent_per_month, 2);
                    $loan_body_pay = round($annoouitet_pay - $loan_percents_pay, 2);
                    $rest_sum = round($rest_sum - $loan_body_pay, 2);
                }
            }

            $new_shedule[$date->format('d.m.Y')] =
                [
                    'pay_sum' => $annoouitet_pay,
                    'loan_percents_pay' => $loan_percents_pay,
                    'loan_body_pay' => $loan_body_pay,
                    'comission_pay' => 0.00,
                    'rest_pay' => $rest_sum
                ];
        }

        $new_shedule['result'] =
            [
                'all_sum_pay' => 0.00,
                'all_loan_percents_pay' => 0.00,
                'all_loan_body_pay' => 0.00,
                'all_comission_pay' => 0.00,
                'all_rest_pay_sum' => 0.00
            ];


        foreach ($new_shedule as $date => $pay) {
            if ($date != 'result') {
                $new_shedule['result']['all_sum_pay'] += round((float)$pay['pay_sum'], 2);
                $new_shedule['result']['all_loan_percents_pay'] += round((float)$pay['loan_percents_pay'], 2);
                $new_shedule['result']['all_loan_body_pay'] += round((float)$pay['loan_body_pay'], 2);
                $new_shedule['result']['all_comission_pay'] += round((float)$pay['comission_pay'], 2);
                $new_shedule['result']['all_rest_pay_sum'] = 0.00;
            }
        }

        $dates[0] = date('d.m.Y', strtotime($order->probably_start_date));
        $payments[0] = -$new_shedule['result']['all_loan_body_pay'];
        $rest_sum = $new_shedule['result']['all_loan_body_pay'];

        foreach ($new_shedule as $date => $pay) {
            if ($date != 'result') {
                $payments[] = round($pay['pay_sum'], '2');
                $dates[] = date('d.m.Y', strtotime($date));
                $rest_sum -= round($pay['loan_body_pay'], 2);
                $new_shedule[$date]['rest_pay'] = $rest_sum;
            }
        }

        foreach ($dates as $date) {
            $date = new DateTime(date('Y-m-d H:i:s', strtotime($date)));

            $new_dates[] = mktime(
                $date->format('H'),
                $date->format('i'),
                $date->format('s'),
                $date->format('m'),
                $date->format('d'),
                $date->format('Y')
            );
        }

        $xirr = round($this->Financial->XIRR($payments, $new_dates) * 100, 3);
        $xirr /= 100;

        $psk = round(((pow((1 + $xirr), (1 / 12)) - 1) * 12) * 100, 3);

        if ($this->request->post('preview') == 1) {

            $payment_schedule_html = '';


            foreach ($new_shedule as $date => $payment) {
                if ($date != 'result') {

                    $paysum = number_format($payment['pay_sum'], 2, ',', ' ');
                    $body_sum = number_format($payment['loan_body_pay'], 2, ',', ' ');
                    $percent_sum = number_format($payment['loan_percents_pay'], 2, ',', ' ');
                    $comission_sum = number_format((float)$payment['comission_pay'], 2, ',', ' ');
                    $rest_sum = number_format($payment['rest_pay'], 2, ',', ' ');

                    $payment_schedule_html .= "<tr>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control daterange' name='date[][date]' value=" . $date . " disabled></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control restructure_pay' name='pay_sum[][pay_sum]' value='$paysum' disabled></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control restructure_od' name='loan_body_pay[][loan_body_pay]' value='$body_sum' disabled></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control restructure_prc' name='loan_percents_pay[][loan_percents_pay]' value='$percent_sum' disabled></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control restructure_cms' name='comission_pay[][comission_pay]' value='$comission_sum' disabled></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control rest_sum' name='rest_pay[][rest_pay]' value='$rest_sum' disabled></td>";
                    $payment_schedule_html .= "</tr>";
                }
            }

            $paysum = number_format($new_shedule['result']['all_sum_pay'], 2, ',', ' ');
            $body_sum = number_format($new_shedule['result']['all_loan_body_pay'], 2, ',', ' ');
            $percent_sum = number_format($new_shedule['result']['all_loan_percents_pay'], 2, ',', ' ');
            $comission_sum = number_format((float)$new_shedule['result']['all_comission_pay'], 2, ',', ' ');
            $rest_sum = number_format($new_shedule['result']['all_rest_pay_sum'], 2, ',', ' ');

            $payment_schedule_html .= "<tr>";
            $payment_schedule_html .= "<td><input type='text' class='form-control daterange' value='ИТОГО' disabled></td>";
            $payment_schedule_html .= "<td><input type='text' class='form-control' value='$paysum' disabled></td>";
            $payment_schedule_html .= "<td><input type='text' class='form-control' value='$body_sum' disabled></td>";
            $payment_schedule_html .= "<td><input type='text' class='form-control' value='$percent_sum' disabled></td>";
            $payment_schedule_html .= "<td><input type='text' class='form-control' value='$comission_sum' disabled></td>";
            $payment_schedule_html .= "<td><input type='text' class='form-control' value='$rest_sum' disabled></td>";
            $payment_schedule_html .= "</tr>";

            echo json_encode(['schedule' => $payment_schedule_html, 'psk' => $psk]);
            exit;

        } else {

            $this->PaymentsSchedules->updates($order->order_id, ['actual' => 0]);

            $order->payment_schedule =
                [
                    'user_id' => $order->user_id,
                    'order_id' => $order->order_id,
                    'contract_id' => $order->contract_id,
                    'created' => date('Y-m-d H:i:s'),
                    'type' => 'restruct',
                    'actual' => 1,
                    'schedule' => json_encode($new_shedule),
                    'psk' => $psk,
                    'comment' => $comment
                ];

            $this->PaymentsSchedules->add($order->payment_schedule);


            $order->restruct_date = date('Y-m-d');
            $order->probably_return_date = $end_date->format('Y-m-d');

            $this->documents->create_document(array(
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'type' => 'DOP_SOGLASHENIE',
                'params' => $order,
                'numeration' => '04.03.3'
            ));

            $this->documents->create_document(array(
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'type' => 'DOP_GRAFIK',
                'params' => $order,
                'numeration' => '04.04.1'
            ));

            $this->users->update_user($order->user_id, ['balance_blocked' => 1]);
            exit;
        }

    }

    private
    function action_send_asp_code()
    {

        $phone = $this->request->post('phone');
        $user_id = $this->request->post('user');
        $order_id = $this->request->post('order');

        $docs = $this->Documents->get_documents(['order_id' => $order_id]);

        if (empty($docs)) {
            echo json_encode(['error' => 'Документы не сформированы, сформировать?']);
            exit;
        }

        $code = random_int(1000, 9999);
        $message = "Ваш код для подписания документов: $code. Сообщите код андеррайтеру РуКреда";
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

    private
    function action_confirm_asp()
    {
        $phone = $this->request->post('phone');
        $code = $this->request->post('code');
        $user_id = $this->request->post('user');
        $order_id = $this->request->post('order');
        $restruct = $this->request->post('restruct');

        $order = $this->orders->get_order($order_id);

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

        } else {
            $this->orders->update_order($order_id, ['sms' => $code]);

            $asp_log =
                [
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'code' => $code,
                    'created' => date('Y-m-d H:i:s'),
                    'recepient' => $phone,
                    'manager_id' => $this->manager->id
                ];

            if (empty($restruct)) {

                $asp_log['type'] = 'sms';

                $this->AspCodes->add_code($asp_log);

                $payment_schedule = $this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);
                $payment_schedule = json_decode($payment_schedule->schedule, true);

                $date = date('Y-m-d');

                foreach ($payment_schedule as $payday => $payment) {
                    if ($payday != 'result') {
                        $payday = date('Y-m-d', strtotime($payday));
                        if ($payday > $date) {
                            $next_payment = $payday;
                            break;
                        }
                    }
                }

                $number = $order->uid;
                $number = explode(' ', $number);

                $contracts = $this->contracts->get_contracts(['user_id' => $order->user_id]);

                if (!empty($contracts)) {
                    $count_contracts = count($contracts);
                    str_pad($count_contracts, 2, '0', STR_PAD_LEFT);
                } else {
                    $count_contracts = '01';
                }

                $loantype = $this->Loantypes->get_loantype($order->loan_type);

                $new_number = "$number[0] $loantype->number $number[1] $count_contracts";


                $contract =
                    [
                        'order_id' => $order->order_id,
                        'user_id' => $order->user_id,
                        'amount' => $order->amount,
                        'number' => $new_number,
                        'period' => $order->period,
                        'base_percent' => $order->percent,
                        'peni_percent' => 0,
                        'status' => 0,
                        'loan_body_summ' => $order->amount,
                        'loan_percents_summ' => 0,
                        'loan_peni_summ' => 0,
                        'issuance_date' => date('Y-m-d H:i:s'),
                        'return_date' => $next_payment
                    ];

                $contract_id = $this->Contracts->add_contract($contract);
                $this->orders->update_order($order->order_id, ['contract_id' => $contract_id]);

                echo json_encode(['success' => 1]);
                exit;
            } else {
                $asp_log['type'] = 'restruct_sms';
                $asp_id = $this->AspCodes->add_code($asp_log);

                $order_id = $this->request->post('order_id');
                $documents = $this->documents->get_documents(['order_id' => $order_id]);

                foreach ($documents as $document) {
                    if (in_array($document->type, ['DOP_GRAFIK', 'DOP_SOGLASHENIE']) && empty($document->asp_id)) {
                        $this->documents->update_document($document->id, ['pre_asp_id' => $asp_id]);
                    }
                }

                echo json_encode(['success' => 1]);
                exit;
            }
        }
    }

    private function action_confirm_restruct()
    {
        $order_id = $this->request->post('order_id');
        $documents = $this->documents->get_documents(['order_id' => $order_id]);

        foreach ($documents as $document) {
            if (in_array($document->type, ['DOP_GRAFIK', 'DOP_SOGLASHENIE']) && empty($document->asp_id)) {
                $this->documents->update_document($document->id, ['asp_id' => $document->pre_asp_id]);
            }
        }

        exit;
    }

    private function action_create_pay_rdr()
    {
        $order_id = $this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $requisits = $this->Requisites->get_requisites(['user_id' => $order->user_id]);

        $default_requisit = new stdClass();

        foreach ($requisits as $requisit) {
            if ($requisit->default == 1)
                $default_requisit = $requisit;
        }

        $payment = new stdClass();
        $payment->order_id = $order_id;
        $payment->date = date('Y-m-d H:i:s');
        $payment->amount = $order->amount;
        $payment->recepient = 9725055162;
        $payment->user_id = $order->user_id;
        $payment->number = '40701810300000000347';
        $payment->description = 'Платежный агент по договору' . $order->uid;
        $payment->user_acc_number = $default_requisit->number;
        $payment->user_bik = $default_requisit->bik;
        $payment->users_inn = $order->inn;

        echo '<pre>';
        print_r($this->Soap1c->send_payment($payment));
        exit;
    }

    private
    function action_send_qr()
    {
        $order_id = $this->request->post('order_id');
        $phone = $this->request->post('phone');
        $contract = $this->contracts->get_order_contract($order_id);

        $payment_schedule = (array)$this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);
        $payment_schedule = json_decode($payment_schedule['schedule'], true);
        $date = date('Y-m-d');

        foreach ($payment_schedule as $payday => $payment) {
            if ($payday != 'result') {
                $payday = date('Y-m-d', strtotime($payday));
                if ($payday > $date) {
                    $next_payment = $payment['pay_sum'];
                    break;
                }
            }
        }

        if (strripos($next_payment, ',') == false) {
            $sum = $next_payment * 100;
        } else {
            list($rub, $kop) = explode(',', $next_payment);
            $rub *= 100;
            $sum = $rub + $kop;
        }

        /*

        $resp = $this->QrGenerateApi->get_qr($sum, 600);
        $pay_link = $resp->results->qr_link;

        */

        $pay_link = $this->Best2pay->get_payment_link($sum, $contract->id);

        $message = "Оплата доступна по ссылке: $pay_link";

        $this->sms->send($phone, $message);
        echo json_encode(['success' => 1]);
        exit;
    }

    private
    function form_docs($order_id, $delete_scans = 1, $asp_id = false)
    {

        if ($delete_scans == 1)
            $this->Scans->delete_all_scans($order_id);

        $this->documents->delete_documents($order_id);

        $order = $this->orders->get_order($order_id);
        $order->payment_schedule = (array)$this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);

        $doc_types =
            [
                '04.05' => 'SOGLASIE_NA_OBR_PERS_DANNIH',
                '04.06' => 'SOGLASIE_RUKRED_RABOTODATEL',
                '03.03' => 'SOGLASIE_RABOTODATEL',
            ];

        if ($order->settlement_id == 2)
            $doc_types['04.05.1'] = 'SOGLASIE_MINB';
        else
            $doc_types['04.05.2'] = 'SOGLASIE_RDB';

        $doc_types['04.07'] = 'SOGLASIE_NA_KRED_OTCHET';
        $doc_types['04.03.02'] = 'INDIVIDUALNIE_USLOVIA';
        $doc_types['04.04'] = 'GRAFIK_OBSL_MKR';
        $doc_types['04.12'] = 'PERECHISLENIE_ZAEMN_SREDSTV';
        $doc_types['04.09'] = 'ZAYAVLENIE_NA_PERECHISL_CHASTI_ZP';
        $doc_types['04.10'] = 'OBSHIE_USLOVIYA';
        $doc_types['03.04'] = 'ZAYAVLENIE_ZP_V_SCHET_POGASHENIYA_MKR';

        if ($asp_id)
            $order->asp = $asp_id;


        foreach ($doc_types as $key => $type) {

            $this->documents->create_document(array(
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'type' => $type,
                'params' => $order,
                'numeration' => (string)$key,
                'asp_id' => $order->asp
            ));
        }
    }

}
