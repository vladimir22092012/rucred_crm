<?php

use Telegram\Bot\Api;
use Viber\Bot;
use Viber\Api\Sender;
use Viber\Client;
use App\Services\MailService;
use App\Services\Encryption;
use PHPMailer\PHPMailer\PHPMailer;

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

                case 'form_restruct_docs':
                    $this->action_form_restruct_docs();
                    break;

                case 'accept_by_employer':
                    $this->action_accept_by_employer();
                    break;

                case 'reject_by_employer':
                    $this->action_reject_by_employer();
                    break;

                case 'question_by_employer':
                    $this->action_question_by_employer();
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

                case 'reject_by_under':
                    $response = $this->action_reject_by_under();
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

                case 'save_restruct':
                    return $this->action_save_restruct();
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

                case 'cards_change':
                    return $this->action_cards_change();
                    break;

                case 'accept_order_by_underwriter':
                    $this->action_accept_order_by_underwriter();
                    break;

                case 'accept_approve_by_under':
                    $this->action_accept_approve_by_under();
                    break;

                case 'reject_by_middle':
                    $this->action_reject_by_middle();
                    break;

                case 'next_schedule_date':
                    $this->action_next_schedule_date();
                    break;

                case 'get_companies':
                    $this->action_get_companies();
                    break;

                case 'get_branches':
                    $this->action_get_branches();
                    break;

                case 'bind_scan':
                    $this->action_bind_scan();
                    break;

                case 'check_restruct_scans':
                    $this->action_check_restruct_scans();
                    break;

                case 'edit_loan_settings':
                    $this->action_edit_loan_settings();
                    break;

                case 'confirm_sms':
                    $this->action_confirm_sms();
                    break;

                case 'requisites_edit':
                    $this->action_requisites_edit();
                    break;

                case 'personal_edit':
                    $this->action_personal_edit();
                    break;

                case 'sendOnecTrigger':
                    $this->actionSendOnecTrigger();
                    break;

                case 'sendYaDiskTrigger':
                    $this->actionSendYaDiskTrigger();
                    break;

                case 'editPdn':
                    $this->action_editPdn();
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

                    $weekdays =
                        [
                            'Mon' => "Пн",
                            'Tue' => "Вт",
                            'Wed' => "Ср",
                            'Thu' => "Чт",
                            'Fri' => "Пт",
                            'Sat' => "Сб",
                            'Sun' => "Вс"
                        ];

                    $weekday = date('D', strtotime($order->probably_start_date));
                    $order->probably_start_weekday = $weekdays[$weekday];

                    $from_registr = $this->request->get('reg');

                    if (!empty($from_registr))
                        $this->design->assign('from_registr', $from_registr);

                    $projectNumber = ProjectContractNumberORM::where('orderId', $order->order_id)->where('userId', $order->user_id)->first();
                    $this->design->assign('projectNumber', $projectNumber);

                    $uploadsLoanOnec = ExchangeCronORM::where('orderId', $order->order_id)->get();
                    $this->design->assign('uploadsLoanOnec', $uploadsLoanOnec);

                    $uploadsPaymentOnec = SendPaymentCronORM::where('order_id', $order->order_id)->get();
                    $this->design->assign('uploadsPaymentOnec', $uploadsPaymentOnec);

                    $uploadsDocsYaDisk = YaDiskCronORM::where('order_id', $order->order_id)->get();
                    $this->design->assign('uploadsDocsYaDisk', $uploadsDocsYaDisk);


                    $old_orders = $this->orders->get_orders(['user_id' => $order->user_id]);

                    $client_status = 'Повтор';

                    if (count($old_orders) > 1) {
                        foreach ($old_orders as $old_order) {
                            if (in_array($old_order->status, [5, 7]))
                                $client_status = 'ПК';
                        }
                    }

                    if (count($old_orders) == 1)
                        $client_status = 'Новая';

                    $this->design->assign('client_status', $client_status);

                    $scroll_to_photo = $this->request->get('scroll');
                    $this->design->assign('scroll_to_photo', $scroll_to_photo);

                    $order->regaddress = $this->addresses->get_address($order->regaddress_id);
                    $order->faktaddress = $this->addresses->get_address($order->faktaddress_id);

                    if (!empty($order->requisite_id)) {
                        $order->requisite = $this->requisites->get_requisite($order->requisite_id);

                        $holder = $order->requisite->holder;

                        if (!empty($holder)) {
                            $holder = explode(' ', $holder, 3);
                            $same_holder = 0;

                            if (count($holder) == 3) {
                                list($holder_name, $holder_firstname, $holder_patronymic) = $holder;
                                if ($order->lastname == $holder_name && $order->firstname == $holder_firstname && $order->patronymic == $holder_patronymic)
                                    $same_holder = 1;
                            }

                            if (count($holder) == 2) {
                                list($holder_name, $holder_firstname) = $holder;
                                if ($order->lastname == $holder_name && $order->firstname == $holder_firstname)
                                    $same_holder = 1;
                            }

                            $this->design->assign('same_holder', $same_holder);
                        }
                    }

                    if (!empty($order->card_id)) {
                        $card = $this->cards->get_card($order->card_id);
                        $order->card_name = $card->name;
                        $order->pan = $card->pan;
                        $order->expdate = $card->expdate;
                    }

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

                    $changelogs = $this->changelogs->get_changelogs(array('order_id' => $order_id, 'sort' => 'date_desc'));
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

        $schedules = $this->PaymentsSchedules->gets($order_id);;

        if (count($schedules) > 1) {

            foreach ($schedules as $key => $schedule) {
                $schedule->schedule = json_decode($schedule->schedule, true);

                uksort($schedule->schedule,
                    function ($a, $b) {

                        if ($a == $b)
                            return 0;

                        return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
                    });

                if ($schedule->actual == 1) {
                    $payment_schedule = end($schedules);
                }
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

        foreach ($payment_schedule->schedule as $payday => $payment) {
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

        foreach ($documents as $document) {
            if (!empty($document->scan_id))
                $document->scan = $this->Scans->get($document->scan_id);
        }

        $need_form_restruct_docs = 0;

        if (in_array($order->status, [5, 17, 19])) {

            $restruct_docs = $this->documents->get_documents(['order_id' => $order->order_id, 'stage_type' => 'restruct', 'asp_flag' => null]);

            if (empty($restruct_docs))
                $need_form_restruct_docs = 1;
        }

        $this->design->assign('need_form_restruct_docs', $need_form_restruct_docs);

        $sort_docs = [];

        foreach ($documents as $document) {
            $key = date('Y-m-d', strtotime($document->created));
            if (empty($document->stage_type))
                $document->stage_type = 'reg-docs';

            $sort_docs[$key][$document->stage_type][] = $document;
        }


        $this->design->assign('sort_docs', $sort_docs);

        $settlement = $this->OrganisationSettlements->get_settlement($order->settlement_id);
        $this->design->assign('settlement', $settlement);

        if ($order->status == 10) {
            $this->db->query("
            SELECT *
            FROM s_transactions
            WHERE order_id = $order->order_id
            AND reference = 'issuance'
            AND reason_code = 0
            ORDER BY id DESC
            LIMIT 1
            ");

            $issuance_transaction = $this->db->result();
            $this->design->assign('issuance_transaction', $issuance_transaction);
        }

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

    private function action_contactperson_status()
    {
        $contact_status = $this->request->post('contact_status', 'integer');
        $contactperson_id = $this->request->post('contactperson_id', 'integer');

        $this->contactpersons->update_contactperson($contactperson_id, array('contact_status' => $contact_status));

        return array('success' => 1, 'contact_status' => $contact_status);
    }

    private function action_workout()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $workout = $this->request->post('workout', 'integer');

        $this->orders->update_order($order_id, array('quality_workout' => $workout));

        return array('success' => 1, 'contact_status' => $contact_status);
    }

    private function confirm_contract_action()
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
    private function accept_order_action()
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

    private function action_reject_by_under()
    {
        $order_id = $this->request->post('order_id');

        $order = $this->orders->get_order($order_id);

        $this->orders->update_order($order_id, ['status' => 20]);

        $this->tickets->update_by_theme_id(18, ['status' => 7], $order_id);
        $this->tickets->update_by_theme_id(8, ['status' => 7], $order_id);
        $this->tickets->update_by_theme_id(48, ['status' => 7], $order_id);

        $communication_theme = $this->CommunicationsThemes->get(47);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => $communication_theme->id,
                'company_id' => $order->company_id,
                'group_id' => 2,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $cron =
            [
                'template_id' => 10,
                'user_id' => $order->user_id,
            ];

        $this->NotificationsClientsCron->add($cron);

        $this->eventlogs->add_log(array(
            'event_id' => 74,
            'manager_id' => $this->manager->id,
            'order_id' => $order_id,
            'user_id' => $order->user_id,
            'created' => date('Y-m-d H:i:s'),
        ));

        exit;
    }

    /**
     * OrderController::approve_order_action()
     * Одобрение заявки
     * @return array
     */
    private function approve_order_action()
    {
        $order_id = $this->request->post('order_id', 'integer');

        $order = $this->orders->get_order((int)$order_id);

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

        $communication_theme = $this->CommunicationsThemes->get(8);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => $communication_theme->id,
                'company_id' => $order->company_id,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $cron =
            [
                'template_id' => 7,
                'user_id' => $order->user_id,
            ];

        $this->NotificationsClientsCron->add($cron);

        $this->tickets->update_by_theme_id(18, ['status' => 4], $order->order_id);

        echo json_encode(['success' => 1]);
        exit;
    }

    /**
     * OrderController::delivery_order_action()
     *  Оплата ордера менеджером
     *
     * @return array
     */
    private function delivery_order_action()
    {
        $order_id = (int)$this->request->post('order_id', 'integer');

        $order = $this->orders->get_order($order_id);

        //отправляем заявку в 1с через крон
        $insert =
            [
                'orderId'       => $order_id,
                'userId'        => $order->user_id,
                'contractId'    => $order->contract_id
            ];

        ExchangeCronORM::insert($insert);

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
                'theme_id' => $communication_theme->id,
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

            $cron =
                [
                    'ticket_id' => $ticket_id,
                    'is_complited' => 0
                ];

            $this->NotificationsCron->add($cron);

            $this->design->assign('order', $order);
            $documents = $this->documents->get_documents(['order_id' => $order->order_id]);
            $docs_email = [];

            foreach ($documents as $document) {
                if (in_array($document->type, ['INDIVIDUALNIE_USLOVIA', 'GRAFIK_OBSL_MKR']))
                    $docs_email[$document->type] = $document->hash;
            }

            $individ_encrypt = $this->config->back_url . '/online_docs?id=' . $docs_email['INDIVIDUALNIE_USLOVIA'];
            $graphic_encrypt = $this->config->back_url . '/online_docs?id=' . $docs_email['GRAFIK_OBSL_MKR'];

            $this->design->assign('individ_encrypt', $individ_encrypt);
            $this->design->assign('graphic_encrypt', $graphic_encrypt);

            $contracts = $this->contracts->get_contracts(['order_id' => $order->order_id]);
            $group = $this->groups->get_group($order->group_id);
            $company = $this->companies->get_company($order->company_id);

            if (!empty($contracts)) {
                $count_contracts = count($contracts);
                $count_contracts = str_pad($count_contracts, 2, '0', STR_PAD_LEFT);
            } else {
                $count_contracts = '01';
            }

            $loantype = $this->Loantypes->get_loantype($order->loan_type);

            $uid = "$group->number$company->number $loantype->number $order->personal_number $count_contracts";
            $this->design->assign('uid', $uid);

            $fetch = $this->design->fetch('email/approved.tpl');

            $mail = new PHPMailer(false);

            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'mail.nic.ru';                          //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = 'noreply@re-aktiv.ru';                  //SMTP username
            $mail->Password = 'HG!_@H#*&!^!HwJSDJ2Wsqgq';             //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
            $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('noreply@re-aktiv.ru');
            $mail->addAddress($order->email);     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'RuCred | Уведомление';
            $mail->Body = $fetch;

            $mail->send();

            $asp_log =
                [
                    'user_id' => $order->user_id,
                    'order_id' => $order->order_id,
                    'created' => date('Y-m-d H:i:s'),
                    'type' => 'rucred_sms',
                    'recepient' => $order->phone_mobile,
                    'manager_id' => $this->manager->id
                ];

            $asp_id = $this->AspCodes->add_code($asp_log);

            $this->documents->update_asp(['order_id' => $order_id, 'rucred_asp_id' => $asp_id, 'second_pak' => 1]);

            $asp_id = $this->AspCodes->get_code(['order_id' => $order_id, 'type' => 'sms']);
            $this->documents->update_asp(['order_id' => $order_id, 'asp_id' => $asp_id->id, 'second_pak' => 1]);

            $cron =
                [
                    'order_id' => $order_id,
                    'pak' => 'second_pak'
                ];

            $this->YaDiskCron->add($cron);

            $this->tickets->update_by_theme_id(12, ['status' => 4], $order_id);

            $cron =
                [
                    'template_id' => 8,
                    'user_id' => $order->user_id,
                ];

            $this->NotificationsClientsCron->add($cron);

            $schedule = $this->PaymentsSchedules->get(['actual' => 1, 'order_id' => $order->order_id]);
            $schedules = json_decode($schedule->schedule, true);
            unset($schedules['result']);

            uksort($schedules,
                function ($a, $b) {

                    if ($a == $b)
                        return 0;

                    return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
                });

            foreach ($schedules as $date => $payment) {
                $graphs_payments =
                    [
                        'order_id' => $order->order_id,
                        'user_id' => $order->user_id,
                        'schedules_id' => $schedule->id,
                        'sum_pay' => $payment['pay_sum'],
                        'od_pay' => $payment['loan_body_pay'],
                        'prc_pay' => $payment['loan_percents_pay'],
                        'com_pay' => $payment['comission_pay'],
                        'rest_pay' => $payment['rest_pay'],
                        'pay_date' => date('d.m.Y', strtotime($date))
                    ];

                $this->PaymentsToSchedules->add($graphs_payments);
            }

            echo json_encode(['success' => 1]);
            exit;
        } else {
            echo json_encode(['error' => $resp]);
            exit;
        }

    }

    /**
     * OrderController::delivery_order_status_action()
     *  Проверка статуса выплаты ордера
     *
     * @return array
     */
    private function delivery_order_status_action()
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

    private function reject_order_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $reason_id = $this->request->post('reason', 'integer');
        $order = $this->orders->get_order((int)$order_id);

        $reason = $this->reasons->get_reason($reason_id);

        $update = array(
            'status' => 20,
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

        $communication_theme = $this->CommunicationsThemes->get(47);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => $communication_theme->id,
                'company_id' => $order->company_id,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $cron =
            [
                'template_id' => 10,
                'user_id' => $order->user_id,
            ];

        $this->NotificationsClientsCron->add($cron);

        return array('success' => 1, 'status' => 20);
    }

    private function status_action($status)
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

    private function addresses_action()
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


    private function action_personal()
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

    private function action_images()
    {
        $user_id = $this->request->post('user_id', 'integer');

        $statuses = $this->request->post('status');

        foreach ($statuses as $file_id => $status) {

            $this->users->update_file($file_id, array('status' => $status));
        }

        $files = $this->users->get_files(array('user_id' => $user_id));

        $this->design->assign('files', $files);
        exit;
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

    public function action_repay()
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

    private function send_sms_action()
    {
        $orderId = $this->request->post('order');

        $order = OrdersORM::find($orderId);

        $code = random_int(1000, 9999);
        $template = $this->sms->get_template(2);
        $message = str_replace('$code', $code, $template->template);
        $response = $this->sms->send(
            $order->user->phone_mobile,
            $message
        );
        $this->db->query('
        INSERT INTO s_sms_messages
        SET phone = ?, code = ?, response = ?, ip = ?, user_id = ?, created = ?
        ', $order->user->phone_mobile, $code, $response['resp'], $_SERVER['REMOTE_ADDR'] ?? '', $order->user->id, date('Y-m-d H:i:s'));

        echo json_encode(['code' => $code]);
        exit;
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

    private function action_edit_schedule()
    {

        $date = $this->request->post('date');
        $schedule_id = $this->request->post('schedule_id');
        $pay_sum = $this->request->post('pay_sum');
        $loan_percents_pay = $this->request->post('loan_percents_pay');
        $loan_body_pay = $this->request->post('loan_body_pay');
        $comission_pay = $this->request->post('comission_pay');
        $rest_pay = $this->request->post('rest_pay');
        $order_id = $this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $user = $this->users->get_user($order->user_id);
        $user = (array)$user;
        $loan = $this->loantypes->get_loantype($order->loan_type);

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

        $all_sum_credits = 0;
        $sum_credits_pay = 0;
        $credits_story = json_decode($user['credits_story']);
        $cards_story = json_decode($user['cards_story']);

        if (!empty($credits_story)) {
            foreach ($credits_story as $credit) {
                $credit->credits_month_pay = preg_replace("/[^,.0-9]/", '', $credit->credits_month_pay);
                if (!empty($credit->credits_month_pay))
                    $sum_credits_pay += $credit->credits_month_pay;
            }

            $all_sum_credits += $sum_credits_pay;
        }

        if (!empty($cards_story)) {
            foreach ($cards_story as $card) {
                $card->cards_rest_sum = preg_replace("/[^,.0-9]/", '', $card->cards_rest_sum);
                $card->cards_limit = preg_replace("/[^,.0-9]/", '', $card->cards_limit);

                if (!empty($card->cards_limit)) {
                    $max = 0.05 * $card->cards_limit;
                } else {
                    $max = 0;
                }
                if (!empty($card->cards_rest_sum)) {
                    $min = 0.1 * $card->cards_rest_sum;
                } else {
                    $min = 0;
                }

                $all_sum_credits += min($max, $min);
            }
        }

        $month_pay = $order->amount * ((1 / $loan->max_period) + (($psk / 100) / 12));

        $all_sum_credits += $month_pay;

        if ($all_sum_credits != 0)
            $pdn = round(($all_sum_credits / $user['income']) * 100, 2);
        else
            $pdn = 0;

        $this->users->update_user($user['id'], ['pdn' => $pdn]);

        $update =
            [
                'psk' => $psk,
                'schedule' => json_encode($payment_schedule)
            ];

        $this->PaymentsSchedules->update($schedule_id, $update);
        exit;
    }

    private function action_change_photo_status()
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
        exit;
    }

    private function action_accept_by_employer()
    {
        $order_id = (int)$this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $this->orders->update_order($order_id, ['status' => 14]);
        $communication_theme = $this->CommunicationsThemes->get(11);
        $this->tickets->update_by_theme_id(8, ['status' => 4], $order_id);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => $communication_theme->id,
                'company_id' => $order->company_id,
                'group_id' => 2,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $this->eventlogs->add_log(array(
            'event_id' => 72,
            'manager_id' => $this->manager->id,
            'order_id' => $order_id,
            'user_id' => $order->user_id,
            'created' => date('Y-m-d H:i:s'),
        ));

        exit;
    }

    private function action_reject_by_employer()
    {
        $order_id = (int)$this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $this->orders->update_order($order_id, ['status' => 15]);
        $this->tickets->update_by_theme_id(8, ['status' => 7], $order_id);

        $communication_theme = $this->CommunicationsThemes->get(48);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => $communication_theme->id,
                'company_id' => $order->company_id,
                'group_id' => 2,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $cron =
            [
                'template_id' => 12,
                'user_id' => $order->user_id,
            ];

        $this->NotificationsClientsCron->add($cron);

        $this->eventlogs->add_log(array(
            'event_id' => 71,
            'manager_id' => $this->manager->id,
            'order_id' => $order_id,
            'user_id' => $order->user_id,
            'created' => date('Y-m-d H:i:s'),
        ));

        exit;
    }

    private function action_question_by_employer()
    {
        $order_id = (int)$this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $this->orders->update_order($order_id, ['status' => 13]);
        $this->tickets->update_by_theme_id(8, ['status' => 4], $order_id);

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
                'theme_id' => $communication_theme->id,
                'company_id' => $order->company_id,
                'group_id' => 2,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $this->tickets->update_by_theme_id(8, ['status' => 4], $order_id);

        $this->eventlogs->add_log(array(
            'event_id' => 73,
            'manager_id' => $this->manager->id,
            'order_id' => $order_id,
            'user_id' => $order->user_id,
            'created' => date('Y-m-d H:i:s'),
        ));

        exit;
    }

    private function action_edit_personal_number()
    {
        $user_id = (int)$this->request->post('user_id');
        $number = (int)$this->request->post('number');

        if (strlen($number) > 6) {
            echo json_encode(['error' => 'Номер не может быть более 6 символов']);
            exit;
        }

        $number = str_pad($number, 6, 0, STR_PAD_LEFT);

        $check = $this->users->check_busy_number($number);

        if ($check && $check != 0) {
            echo json_encode(['error' => 'Такой номер уже есть']);
            exit;
        } else {

            $query = $this->db->placehold("
            SELECT id, uid
            FROM s_orders
            WHERE user_id = $user_id
            ");

            $this->db->query($query);
            $orders = $this->db->results();

            foreach ($orders as $order) {
                $uid = explode(' ', $order->uid);
                $uid[1] = (string)$number;
                $uid = implode(' ', $uid);

                $this->orders->update_order($order->id, ['uid' => $uid]);
            }

            $this->users->update_user($user_id, ['personal_number' => $number]);
            echo json_encode(['success' => 1]);
            exit;
        }
    }

    private function action_change_loan_settings()
    {
        $order_id = (int)$this->request->post('order_id');
        $amount = $this->request->post('amount');
        $loan_tarif = $this->request->post('loan_tarif');
        $probably_start_date = $this->request->post('probably_start_date');
        $loantype = $this->Loantypes->get_loantype((int)$loan_tarif);
        $order = $this->orders->get_order($order_id);
        $delete_restruct = $this->request->post('delete_restruct');

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

        if (!empty($delete_restruct)) {
            $this->db->query("
            DELETE FROM s_documents
            WHERE `type` in ('ZAYAVLENIE_RESTRUCT', 'OBSHIE_USLOVIYA_REST', 'DOP_GRAFIK', 'DOP_SOGLASHENIE')
            AND order_id = ?
            AND ready = 0
            ", $order_id);

            $this->db->query("
            DELETE FROM s_payments_schedules
            WHERE `type` = 'restruct'
            AND order_id = ?
            AND actual = 1
            ", $order_id);

            $this->db->query("
            UPDATE s_payments_schedules
            SET actual = 1
            WHERE order_id = ?
            ORDER BY id DESC 
            LIMIT 1
            ", $order_id);

            $this->orders->update_order($order_id, ['status' => 5]);
        }
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_reform_schedule($order_id)
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
        $start_date = new DateTime(date('Y-m-d', strtotime($order['probably_start_date'])));
        $paydate = new DateTime(date('Y-m-' . "$first_pay_day", strtotime($start_date->format('Y-m-d'))));
        $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);

        if ($start_date > $paydate || date_diff($paydate, $start_date)->days <= $loan->free_period)
            $paydate->add(new DateInterval('P1M'));

        $percent_per_month = (($order['percent'] / 100) * 365) / 12;
        $annoouitet_pay = $order['amount'] * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$loan->max_period)));
        $annoouitet_pay = round($annoouitet_pay, '2');

        $iteration = 0;

        $count_days_this_month = date('t', strtotime($start_date->format('Y-m-d')));
        $paydate = $this->check_pay_date(new DateTime($paydate->format('Y-m-' . $first_pay_day)));

        if (date_diff($paydate, $start_date)->days <= $loan->free_period) {
            $plus_loan_percents = round(($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $start_date)->days, 2);
            $sum_pay = $annoouitet_pay + $plus_loan_percents;
            $loan_percents_pay = round(($rest_sum * $percent_per_month) + $plus_loan_percents, 2, PHP_ROUND_HALF_DOWN);
            $body_pay = $sum_pay - $loan_percents_pay;
            $paydate->add(new DateInterval('P1M'));
            $iteration++;
        } elseif (date_diff($paydate, $start_date)->days >= $loan->min_period && date_diff($paydate, $start_date)->days < $count_days_this_month) {
            $minus_percents = ($order['percent'] / 100) * $order['amount'] * ($count_days_this_month - date_diff($paydate, $start_date)->days);
            $sum_pay = $annoouitet_pay - round($minus_percents, 2);
            $loan_percents_pay = ($rest_sum * $percent_per_month) - $minus_percents;
            $loan_percents_pay = round($loan_percents_pay, 2, PHP_ROUND_HALF_DOWN);
            $body_pay = $sum_pay - $loan_percents_pay;
            $iteration++;
        } elseif (date_diff($paydate, $start_date)->days >= $count_days_this_month) {
            $sum_pay = $annoouitet_pay;
            $loan_percents_pay = round($rest_sum * $percent_per_month, 2, PHP_ROUND_HALF_DOWN);
            $body_pay = round($sum_pay - $loan_percents_pay, 2);
            $iteration++;
        } else {
            $sum_pay = ($order['percent'] / 100) * $order['amount'] * date_diff($paydate, $start_date)->days;
            $loan_percents_pay = $sum_pay;
            $body_pay = 0.00;
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

        $period = $loan->max_period;
        $period -= $iteration;


        if ($rest_sum != 0) {

            for ($i = 1; $i <= $period; $i++) {
                $paydate->setDate($paydate->format('Y'), $paydate->format('m'), $first_pay_day);
                $date = $this->check_pay_date($paydate);

                if ($i == $period && $loan->id != 1) {
                    $loan_body_pay = $rest_sum;
                    $loan_percents_pay = $annoouitet_pay - $loan_body_pay;
                    $rest_sum = 0.00;
                } elseif ($loan->id == 1) {
                    $loan_body_pay = $rest_sum;
                    $loan_percents_pay = $order['amount'] * ($order['percent']/100) * date_diff($start_date, $date)->days - $loan_percents_pay;
                    $annoouitet_pay = $loan_body_pay + $loan_percents_pay;
                    $rest_sum = 0.00;
                }else {
                    $loan_percents_pay = round($rest_sum * $percent_per_month, 2, PHP_ROUND_HALF_DOWN);
                    $loan_body_pay = round($annoouitet_pay - $loan_percents_pay, 2);
                    $rest_sum = round($rest_sum - $loan_body_pay, 2);
                }

                if (isset($payment_schedule[$date->format('d.m.Y')])) {

                    $date = $this->add_month($date->format('d.m.Y'), 2);
                    $paydate->setDate($date->format('Y'), $date->format('m'), $first_pay_day);
                    $date = $this->check_pay_date($paydate);
                }

                $payment_schedule[$date->format('d.m.Y')] =
                    [
                        'pay_sum' => $annoouitet_pay,
                        'loan_percents_pay' => $loan_percents_pay,
                        'loan_body_pay' => $loan_body_pay,
                        'comission_pay' => 0.00,
                        'rest_pay' => $rest_sum
                    ];

                $probablyReturnDate = $date->format('d.m.Y');

                $paydate->add(new DateInterval('P1M'));
            }
        }

        OrdersORM::where('id', $order_id)->update(['probably_return_date' => date('Y-m-d H:i:s', strtotime($probablyReturnDate))]);

        $payment_schedule['result'] =
            [
                'all_sum_pay' => 0.00,
                'all_loan_percents_pay' => 0.00,
                'all_loan_body_pay' => 0.00,
                'all_comission_pay' => 0.00,
                'all_rest_pay_sum' => 0.00
            ];

        $dates[0] = $start_date->format('Y-m-d');
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

        $all_sum_credits = 0;
        $sum_credits_pay = 0;
        $credits_story = json_decode($user['credits_story']);
        $cards_story = json_decode($user['cards_story']);

        if (!empty($credits_story)) {
            foreach ($credits_story as $credit) {
                $credit->credits_month_pay = preg_replace("/[^,.0-9]/", '', $credit->credits_month_pay);
                if (!empty($credit->credits_month_pay))
                    $sum_credits_pay += $credit->credits_month_pay;
            }

            $all_sum_credits += $sum_credits_pay;
        }

        if (!empty($cards_story)) {
            foreach ($cards_story as $card) {
                $card->cards_rest_sum = preg_replace("/[^,.0-9]/", '', $card->cards_rest_sum);
                $card->cards_limit = preg_replace("/[^,.0-9]/", '', $card->cards_limit);

                if (!empty($card->cards_limit)) {
                    $max = 0.05 * $card->cards_limit;
                } else {
                    $max = 0;
                }
                if (!empty($card->cards_rest_sum)) {
                    $min = 0.1 * $card->cards_rest_sum;
                } else {
                    $min = 0;
                }

                $all_sum_credits += min($max, $min);
            }
        }

        $month_pay = $order['amount'] * ((1 / $loan->max_period) + (($psk / 100) / 12));

        $all_sum_credits += $month_pay;

        if ($all_sum_credits != 0)
            $pdn = round(($all_sum_credits / $user['income']) * 100, 2);
        else
            $pdn = 0;

        $this->users->update_user($user['id'], ['pdn' => $pdn]);

        $schedule = json_encode($payment_schedule);

        $actual_schedule = $this->PaymentsSchedules->get(['order_id' => $order_id, 'actual' => 1]);
        $this->PaymentsSchedules->update($actual_schedule->id, ['psk' => $psk, 'schedule' => $schedule]);

        $this->form_docs($order_id);
    }

    private function check_pay_date($date)
    {
        $clone_date = clone $date;

        for ($i = 0; $i <= 15; $i++) {

            $check_date = $this->WeekendCalendar->check_date($clone_date->format('Y-m-d'));

            if ($check_date == null) {
                break;
            } else {
                $clone_date->sub(new DateInterval('P1D'));
            }
        }

        return $clone_date;
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

    private function check_date($start_date, $loan_id, $first_pay_day = 10)
    {
        $loan = $this->Loantypes->get_loantype($loan_id);

        $start_date = date('Y-m-d', strtotime($start_date));
        $first_pay = new DateTime(date('Y-m-10', strtotime($start_date)));
        $end_date = date('Y-m-' . $first_pay_day, strtotime($start_date . '+' . $loan->max_period . 'month'));

        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);

        if ($start_date > $first_pay) {
            $first_pay->add(new DateInterval('P1M'));
        }

        $first_pay = $this->check_pay_date($first_pay);

        if (date_diff($first_pay, $start_date)->days <= $loan->min_period && $first_pay->format('m') != $start_date->format('m')) {
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

            if (strtotime($pay_date) == strtotime($date)) {
                break;
            } else
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

    private function action_do_restruct()
    {
        $order_id = $this->request->post('order_id');
        $new_term = $this->request->post('new_term');

        $branch_id = $this->request->post('branch');

        $pay_amount = $this->request->post('pay_amount');
        $pay_amount = str_replace([' ', ','], ['', '.'], $pay_amount);

        $comission_amount = $this->request->post('comission');
        $comission_amount = str_replace([' ', ','], ['', '.'], $comission_amount);

        $pay_amount -= $comission_amount;

        $pay_date = date('d.m.Y', strtotime($this->request->post('pay_date')));
        $last_pay_date = date('d.m.Y', strtotime($this->request->post('last_pay_date')));

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
        $new_loan = $order->amount;
        $percent_pay = 0.00;
        $body_pay = 0.00;
        $lost_percents = 0.00;

        foreach ($payment_schedule as $date => $schedule) {

            $date = date('d.m.Y', strtotime($date));

            if (strtotime($pay_date) <= strtotime($date)) {
                $schedule['pay_sum'] += $lost_percents;
                $schedule['loan_percents_pay'] += $lost_percents;

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
                        $body_pay = 0.00;
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
                    $body_pay = $schedule['loan_body_pay'];
                    $percent_pay = $schedule['loan_percents_pay'];
                    $new_loan -= $schedule['loan_body_pay'];
                }

                $new_shedule[$date] =
                    [
                        'pay_sum' => $body_pay + $percent_pay + $comission_amount,
                        'loan_body_pay' => $body_pay,
                        'loan_percents_pay' => $percent_pay,
                        'comission_pay' => $comission_amount,
                        'rest_pay' => $new_loan
                    ];

                $last_date = $date;
                break;
            } elseif (strtotime($last_pay_date) >= strtotime($date)) {
                $new_shedule[$date] = $schedule;
                $new_loan -= round($schedule['loan_body_pay'], 2);
            } else {
                $new_shedule[$date] =
                    [
                        'pay_sum' => 0.00,
                        'loan_body_pay' => 0.00,
                        'loan_percents_pay' => 0.00,
                        'comission_pay' => 0.00,
                        'rest_pay' => $new_loan,
                    ];

                $lost_percents += $schedule['loan_percents_pay'];
            }

            $i++;
        }

        if (empty($branch_id)) {
            $user = (array)$this->users->get_user($order->user_id);
            $change_employer = 0;

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
        } else {
            $branch = $this->Branches->get_branch($branch_id);
            $first_pay_day = $branch->payday;
            $change_employer = 1;
        }

        $start_date = date('Y-m-' . $first_pay_day, strtotime($last_date . '+1 month'));
        $start_date = new DateTime($start_date);
        $last_date = new DateTime($last_date);

        $percent_per_month = (($order->percent / 100) * 365) / 12;
        $annoouitet_pay = $new_loan * ($percent_per_month / (1 - pow((1 + $percent_per_month), -$new_term)));
        $annoouitet_pay = round($annoouitet_pay, '2');

        if (!isset($plus_percents))
            $plus_percents = 0;

        for ($i = 1; $i <= $new_term; $i++) {

            $start_date->setDate($start_date->format('Y'), $start_date->format('m'), $first_pay_day);
            $start_date = $this->check_pay_date($start_date);

            if ($i == $new_term && $new_term != 1) {
                $loan_body_pay = $new_loan;
                $loan_percents_pay = $annoouitet_pay - $loan_body_pay;
                $new_loan = 0.00;
            } elseif ($new_term == 1) {
                $loan_body_pay = $new_loan;
                $loan_percents_pay = (($order->percent * $new_loan) * date_diff($start_date, $last_date)->days) / 100;
                $new_loan = 0.00;
            } else {
                $loan_percents_pay = round($new_loan * $percent_per_month, 2, PHP_ROUND_HALF_DOWN);
                $loan_body_pay = round($annoouitet_pay - $loan_percents_pay, 2);
                $new_loan = round($new_loan - $loan_body_pay, 2);
            }


            if (isset($new_shedule[$start_date->format('d.m.Y')])) {
                $start_date = $this->add_month($start_date->format('d.m.Y'), 2);
                $start_date->setDate($start_date->format('Y'), $start_date->format('m'), $first_pay_day);
                $start_date = $this->check_pay_date($start_date);
            }

            $new_shedule[$start_date->format('d.m.Y')] =
                [
                    'pay_sum' => $annoouitet_pay + $plus_percents,
                    'loan_percents_pay' => $loan_percents_pay + $plus_percents,
                    'loan_body_pay' => $loan_body_pay,
                    'comission_pay' => 0.00,
                    'rest_pay' => $new_loan
                ];

            $plus_percents = 0;

            $start_date->add(new DateInterval('P1M'));
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

                if (!isset($new_shedule[$date]['rest_pay']) && $new_shedule[$date]['loan_body_pay'] != 0) {
                    $rest_sum -= round($pay['loan_body_pay'], 2);
                    $new_shedule[$date]['rest_pay'] = $rest_sum;
                }
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

            $weekdays =
                [
                    'Mon' => "Пн",
                    'Tue' => "Вт",
                    'Wed' => "Ср",
                    'Thu' => "Чт",
                    'Fri' => "Пт",
                    'Sat' => "Сб",
                    'Sun' => "Вс"
                ];


            foreach ($new_shedule as $date => $payment) {
                if ($date != 'result') {

                    $paysum = number_format($payment['pay_sum'], 2, ',', ' ');
                    $body_sum = number_format($payment['loan_body_pay'], 2, ',', ' ');
                    $percent_sum = number_format($payment['loan_percents_pay'], 2, ',', ' ');
                    $comission_sum = number_format((float)$payment['comission_pay'], 2, ',', ' ');
                    $rest_sum = number_format($payment['rest_pay'], 2, ',', ' ');

                    $weekday = date('D', strtotime($date));
                    $weekday = $weekdays[$weekday];

                    $payment_schedule_html .= "<tr>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control daterange' name='date[][date]' value='$date ($weekday)'</td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control restructure_pay_sum' name='pay_sum[][pay_sum]' value='$paysum' readonly></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control restructure_od' name='loan_body_pay[][loan_body_pay]' value='$body_sum'></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control restructure_prc' name='loan_percents_pay[][loan_percents_pay]' value='$percent_sum'></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control restructure_cms' name='comission_pay[][comission_pay]' value='$comission_sum'></td>";
                    $payment_schedule_html .= "<td><input type='text' class='form-control rest_sum' name='rest_pay[][rest_pay]' value='$rest_sum' readonly></td>";
                    $payment_schedule_html .= "</tr>";
                }
            }

            $paysum = number_format($new_shedule['result']['all_sum_pay'], 2, ',', ' ');
            $body_sum = number_format($new_shedule['result']['all_loan_body_pay'], 2, ',', ' ');
            $percent_sum = number_format($new_shedule['result']['all_loan_percents_pay'], 2, ',', ' ');
            $comission_sum = number_format((float)$new_shedule['result']['all_comission_pay'], 2, ',', ' ');
            $rest_sum = number_format($new_shedule['result']['all_rest_pay_sum'], 2, ',', ' ');

            $payment_schedule_html .= "<tr>";
            $payment_schedule_html .= "<td><input type='text' class='form-control daterange' value='ИТОГО' readonly></td>";
            $payment_schedule_html .= "<td><input type='text' name='result[all_sum_pay]' class='form-control' value='$paysum' readonly></td>";
            $payment_schedule_html .= "<td><input type='text' name='result[all_loan_body_pay]' class='form-control' value='$body_sum' readonly></td>";
            $payment_schedule_html .= "<td><input type='text' name='result[all_loan_percents_pay]' class='form-control' value='$percent_sum' readonly></td>";
            $payment_schedule_html .= "<td><input type='text' name='result[all_comission_pay]' class='form-control' value='$comission_sum' readonly></td>";
            $payment_schedule_html .= "<td><input type='text' name='result[all_rest_pay_sum]' class='form-control' value='$rest_sum' readonly></td>";
            $payment_schedule_html .= "</tr>";

            echo json_encode(
                [
                    'pay_date' => $pay_date,
                    'schedule' => $payment_schedule_html,
                    'psk' => $psk,
                    'new_loan' => $new_shedule['result']['all_loan_body_pay'],
                    'change_employer' => $change_employer
                ]);

            exit;

        }

    }

    private function action_save_restruct()
    {
        $date = $this->request->post('date');
        $pay_sum = $this->request->post('pay_sum');
        $loan_percents_pay = $this->request->post('loan_percents_pay');
        $loan_body_pay = $this->request->post('loan_body_pay');
        $comission_pay = $this->request->post('comission_pay');
        $rest_pay = $this->request->post('rest_pay');
        $order_id = $this->request->post('order_id');
        $comment = $this->request->post('comment');
        $change_employer = $this->request->post('change_employer');

        $restruct_date = $this->request->post('restruct_date');

        $order = $this->orders->get_order($order_id);

        $user = $this->users->get_user($order->user_id);
        $user = (array)$user;

        $results['result'] = $this->request->post('result');

        $payment_schedule = array_replace_recursive($date, $pay_sum, $loan_percents_pay, $loan_body_pay, $comission_pay, $rest_pay);

        foreach ($payment_schedule as $date => $payment) {
            $payment['date'] = explode('(', $payment['date']);
            $payment['date'] = $payment['date'][0];
            $payment_schedule[$payment['date']] = array_slice($payment, 1);
            $payment_schedule[$payment['date']]['pay_sum'] = str_replace([" ", " ", ","], ['', '', '.'], $payment['pay_sum']);
            $payment_schedule[$payment['date']]['loan_percents_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $payment['loan_percents_pay']);
            $payment_schedule[$payment['date']]['loan_body_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $payment['loan_body_pay']);
            $payment_schedule[$payment['date']]['rest_pay'] = str_replace([" ", " ", ","], ['', '', '.'], $payment['rest_pay']);

            if (strtotime($payment['date']) == strtotime($restruct_date)) {
                $payment_schedule[$payment['date']]['last_pay'] = 1;
                $payment_schedule[$payment['date']]['change_employer'] = $change_employer;
            }

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

        $term = count($payment_schedule);
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

        $all_sum_credits = 0;
        $sum_credits_pay = 0;
        $credits_story = json_decode($user['credits_story']);
        $cards_story = json_decode($user['cards_story']);


        if (!empty($credits_story)) {
            foreach ($credits_story as $credit) {
                $credit->credits_month_pay = preg_replace("/[^,.0-9]/", '', $credit->credits_month_pay);
                if (!empty($credit->credits_month_pay))
                    $sum_credits_pay += $credit->credits_month_pay;
            }

            $all_sum_credits += $sum_credits_pay;
        }

        if (!empty($cards_story)) {
            foreach ($cards_story as $card) {
                $card->cards_rest_sum = preg_replace("/[^,.0-9]/", '', $card->cards_rest_sum);
                $card->cards_limit = preg_replace("/[^,.0-9]/", '', $card->cards_limit);

                if (!empty($card->cards_limit)) {
                    $max = 0.05 * $card->cards_limit;
                } else {
                    $max = 0;
                }
                if (!empty($card->cards_rest_sum)) {
                    $min = 0.1 * $card->cards_rest_sum;
                } else {
                    $min = 0;
                }

                $all_sum_credits += min($max, $min);
            }
        }

        $month_pay = $order->amount * ((1 / $term) + (($psk / 100) / 12));

        $all_sum_credits += $month_pay;

        if ($all_sum_credits != 0)
            $pdn = round(($all_sum_credits / $user['income']) * 100, 2);
        else
            $pdn = 0;

        $this->users->update_user($order->user_id, ['balance_blocked' => 1]);

        $this->PaymentsSchedules->updates($order->order_id, ['actual' => 0]);
        $this->PaymentsSchedules->delete_unconfirmed(['order_id' => $order->order_id, 'confirmed' => 0, 'type' => 'restruct']);

        $order->payment_schedule =
            [
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'contract_id' => $order->contract_id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'restruct',
                'actual' => 1,
                'schedule' => json_encode($payment_schedule),
                'psk' => $psk,
                'pdn' => $pdn,
                'comment' => $comment
            ];

        $this->PaymentsSchedules->add($order->payment_schedule);
        $this->orders->update_order($order->order_id, ['status' => 17]);

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_send_asp_code()
    {

        $phone = $this->request->post('phone');
        $user_id = $this->request->post('user');
        $order_id = $this->request->post('order');
        $restruct = $this->request->post('restruct');

        $docs = $this->Documents->get_documents(['order_id' => $order_id]);

        if (empty($docs)) {
            echo json_encode(['error' => 'Документы не сформированы, сформировать?']);
            exit;
        }

        $code = random_int(1000, 9999);

        if (!empty($restruct))
            $template = $this->sms->get_template(6);
        else
            $template = $this->sms->get_template(2);

        $message = str_replace('$code', $code, $template->template);
        $response = $this->sms->send(
            $phone,
            $message
        );
        $this->db->query('
        INSERT INTO s_sms_messages
        SET phone = ?, code = ?, response = ?, ip = ?, user_id = ?, created = ?
        ', $phone, $code, $response['resp'], $_SERVER['REMOTE_ADDR'] ?? '', $user_id, date('Y-m-d H:i:s'));

        echo json_encode(['success' => $code]);
        exit;

    }

    private function action_confirm_asp()
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

                $projectNumber = ProjectContractNumberORM::where('orderId', $order->order_id)->first();

                $contract =
                    [
                        'order_id' => $order->order_id,
                        'user_id' => $order->user_id,
                        'amount' => $order->amount,
                        'number' => $projectNumber->uid,
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

                $this->db->query("
                SELECT id
                FROM s_asp_codes
                WHERE order_id = ?
                AND `type` = 'sms'
                ORDER BY id DESC
                LIMIT 1
                ", $order_id);

                $asp_id = $this->db->result('id');

                $this->documents->update_asp(['order_id' => $order_id, 'asp_id' => $asp_id, 'first_pak' => 1]);

                $cron =
                    [
                        'order_id' => $order_id,
                        'pak' => 'first_pak'
                    ];

                $this->YaDiskCron->add($cron);

                $this->orders->update_order($order->order_id, ['status' => 1, 'contract_id' => $contract_id]);
                $this->add_first_ticket($order->order_id, $order->user_id);


                echo json_encode(['success' => 1]);
                exit;
            } else {
                $asp_id = $this->AspCodes->add_code($asp_log);
                $documents = $this->documents->get_documents(['order_id' => $order_id]);

                foreach ($documents as $document) {
                    if (in_array($document->type, ['DOP_GRAFIK', 'DOP_SOGLASHENIE', 'OBSHIE_USLOVIYA_REST', 'ZAYAVLENIE_RESTRUCT']) && empty($document->asp_id)) {
                        $this->documents->update_document($document->id, ['asp_id' => $asp_id]);
                    }
                }

                $this->orders->update_order($order->order_id, ['status' => 18]);

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
                        'theme_id' => $communication_theme->id,
                        'company_id' => 3,
                        'group_id' => 2,
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

                $cron =
                    [
                        'ticket_id' => $ticket_id,
                        'is_complited' => 0
                    ];

                $this->NotificationsCron->add($cron);

                echo json_encode(['success' => 1]);
                exit;
            }
        }
    }

    private function action_confirm_restruct()
    {
        $order_id = $this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $documents = $this->documents->get_documents(['order_id' => $order_id]);

        $asp_log =
            [
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'rucred_sms',
                'recepient' => $order->phone_mobile,
                'manager_id' => $this->manager->id
            ];

        $rucred_asp_id = $this->AspCodes->add_code($asp_log);

        foreach ($documents as $document) {
            if (in_array($document->type, ['DOP_GRAFIK', 'DOP_SOGLASHENIE', 'OBSHIE_USLOVIYA_REST', 'ZAYAVLENIE_RESTRUCT']) && empty($document->rucred_asp_id)) {
                $this->documents->update_document($document->id, ['rucred_asp_id' => $rucred_asp_id]);
            }
        }

        $communication_theme = $this->CommunicationsThemes->get(31);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => $communication_theme->id,
                'company_id' => 3,
                'group_id' => 2,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $this->orders->update_order($order_id, ['status' => 19]);

        $this->db->query("
            UPDATE s_documents
            SET ready = 1
            WHERE `type` in ('ZAYAVLENIE_RESTRUCT', 'OBSHIE_USLOVIYA_REST', 'DOP_GRAFIK', 'DOP_SOGLASHENIE')
            AND order_id = ?
            ", $order_id);

        exit;
    }

    private function action_create_pay_rdr()
    {
        $order_id = $this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $contract = $this->contracts->get_contract($order->contract_id);
        $requisits = $this->Requisites->get_requisites(['user_id' => $order->user_id]);
        $order->probably_start_date = date('d.m.Y', strtotime($order->probably_start_date));

        //отправляем заявку в 1с через крон
        $insert =
            [
                'orderId'       => $order_id,
                'userId'        => $order->user_id,
                'contractId'    => $order->contract_id
            ];

        ExchangeCronORM::insert($insert);

        $fio = "$order->lastname $order->firstname $order->patronymic";

        $default_requisit = new stdClass();

        foreach ($requisits as $requisit) {
            if ($requisit->default == 1)
                $default_requisit = $requisit;
        }

        $description = "Оплата по договору микрозайма № $contract->number от $order->probably_start_date
            // заемщик $fio ИНН $order->inn. Без налога (НДС)";

        $transaction =
            [
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'amount' => $order->amount * 100,
                'description' => $description,
                'reference' => 'issuance',
                'created' => date('Y-m-d H:i:s')
            ];

        $transaction_id = $this->Transactions->add_transaction($transaction);

        //отправляем платежку в 1с через крон
        $insert =
            [
                'transaction_id' => $transaction_id,
                'order_id'       => $order_id,
                'user_id'        => $order->user_id,
                'contract_id'    => $order->contract_id,
                'requisites_id'  => $default_requisit->id
            ];

        SendPaymentCronORM::insert($insert);

        $asp_log =
            [
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'created' => date('Y-m-d H:i:s'),
                'type' => 'rucred_sms',
                'recepient' => $order->phone_mobile,
                'manager_id' => $this->manager->id
            ];

        $asp_id = $this->AspCodes->add_code($asp_log);

        $this->documents->update_asp(['order_id' => $order_id, 'rucred_asp_id' => $asp_id, 'second_pak' => 1]);
        $asp_id = $this->AspCodes->get_code(['order_id' => $order_id, 'type' => 'sms']);
        $this->documents->update_asp(['order_id' => $order_id, 'asp_id' => $asp_id->id, 'second_pak' => 1]);

        $cron =
            [
                'order_id' => $order_id,
                'pak' => 'second_pak'
            ];

        $this->YaDiskCron->add($cron);

        $communication_theme = $this->CommunicationsThemes->get(17);


        $ticket = [
            'creator' => $order->manager_id,
            'creator_company' => 2,
            'client_lastname' => $order->lastname,
            'client_firstname' => $order->firstname,
            'client_patronymic' => $order->patronymic,
            'head' => $communication_theme->head,
            'text' => $communication_theme->text,
            'theme_id' => 17,
            'company_id' => 2,
            'group_id' => $order->group_id,
            'order_id' => $order->order_id,
            'status' => 0
        ];

        $ticket_id = $this->Tickets->add_ticket($ticket);

        $message =
            [
                'message' => $communication_theme->text,
                'ticket_id' => $ticket_id,
                'manager_id' => $order->manager_id,
            ];

        $this->TicketMessages->add_message($message);

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $cron =
            [
                'template_id' => 8,
                'user_id' => $order->user_id,
            ];

        $this->NotificationsClientsCron->add($cron);

        $this->design->assign('order', $order);
        $documents = $this->documents->get_documents(['order_id' => $order->order_id]);
        $docs_email = [];

        foreach ($documents as $document) {
            if (in_array($document->type, ['INDIVIDUALNIE_USLOVIA', 'GRAFIK_OBSL_MKR', 'INDIVIDUALNIE_USLOVIA_ONL'])) {
                $docs_email[$document->type] = $document->hash;
            }
        }

        $individ_encrypt = $this->config->back_url . '/online_docs?id=' . $docs_email['INDIVIDUALNIE_USLOVIA'];
        $graphic_encrypt = $this->config->back_url . '/online_docs?id=' . $docs_email['GRAFIK_OBSL_MKR'];

        $this->design->assign('individ_encrypt', $individ_encrypt);
        $this->design->assign('graphic_encrypt', $graphic_encrypt);

        $contracts = $this->contracts->get_contracts(['order_id' => $order->order_id]);
        $group = $this->groups->get_group($order->group_id);
        $company = $this->companies->get_company($order->company_id);

        if (!empty($contracts)) {
            $count_contracts = count($contracts);
            $count_contracts = str_pad($count_contracts, 2, '0', STR_PAD_LEFT);
        } else {
            $count_contracts = '01';
        }

        $loantype = $this->Loantypes->get_loantype($order->loan_type);

        $uid = "$group->number$company->number $loantype->number $order->personal_number $count_contracts";
        $this->design->assign('uid', $uid);

        $fetch = $this->design->fetch('email/approved.tpl');

        $mail = new PHPMailer(false);

        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'mail.nic.ru';                          //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'noreply@re-aktiv.ru';                  //SMTP username
        $mail->Password = 'HG!_@H#*&!^!HwJSDJ2Wsqgq';             //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
        $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('noreply@re-aktiv.ru');
        $mail->addAddress($order->email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'RuCred | Уведомление';
        $mail->Body = $fetch;

        $mail->send();

        $schedule = $this->PaymentsSchedules->get(['actual' => 1, 'order_id' => $order->order_id]);
        $schedules = json_decode($schedule->schedule, true);
        unset($schedules['result']);

        uksort($schedules,
            function ($a, $b) {

                if ($a == $b)
                    return 0;

                return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
            });

        foreach ($schedules as $date => $payment) {
            $graphs_payments =
                [
                    'order_id' => $order->order_id,
                    'user_id' => $order->user_id,
                    'schedules_id' => $schedule->id,
                    'sum_pay' => $payment['pay_sum'],
                    'od_pay' => $payment['loan_body_pay'],
                    'prc_pay' => $payment['loan_percents_pay'],
                    'com_pay' => $payment['comission_pay'],
                    'rest_pay' => $payment['rest_pay'],
                    'pay_date' => date('d.m.Y', strtotime($date))
                ];

            $this->PaymentsToSchedules->add($graphs_payments);
        }

        $this->tickets->update_by_theme_id(12, ['status' => 4], $order_id);
        $this->orders->update_order($order_id, ['status' => 5]);

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_send_qr()
    {
        $order_id = $this->request->post('order_id');
        $contract = $this->contracts->get_order_contract($order_id);
        $order = $this->orders->get_order($order_id);

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

        $pay_link = $this->Best2pay->get_payment_link($sum, $contract->id);

        $user_preferred = $this->UserContactPreferred->get($order->user_id);

        if (empty($user_preferred))
            exit;

        $template = $this->sms->get_template(3);
        $template->template = str_replace('$pay_link', $pay_link, $template->template);

        foreach ($user_preferred as $preferred) {
            switch ($preferred->contact_type_id) {

                case 1:
                    $message = $template->template;
                    $this->sms->send(
                        $order->phone_mobile,
                        $message
                    );
                    break;

                case 2:
                    $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
                    $mailService->send(
                        'rucred@ucase.live',
                        $order->email,
                        'RuCred | Уведомление',
                        "$template->template",
                        "<h2>$template->template</h2>"
                    );
                    break;

                case 3:
                    $telegram = new Api($this->config->telegram_token);
                    $telegram_check = $this->TelegramUsers->get($order->user_id, 0);

                    if (!empty($telegram_check)) {
                        $telegram->sendMessage(['chat_id' => $telegram_check->chat_id, 'text' => $template->template]);
                    }
                    break;

                case 4:
                    $bot = new Bot(['token' => $this->config->viber_token]);

                    $botSender = new Sender([
                        'name' => 'Whois bot',
                        'avatar' => 'https://developers.viber.com/img/favicon.ico',
                    ]);
                    $viber_check = $this->ViberUsers->get($order->user_id, 0);

                    if (!empty($viber_check)) {
                        $bot->getClient()->sendMessage(
                            (new \Viber\Api\Message\Text())
                                ->setSender($botSender)
                                ->setReceiver($viber_check->chat_id)
                                ->setText($template->template)
                        );
                    }
                    break;
            }
        }

        echo json_encode(['success' => 1]);
        exit;
    }

    private function form_docs($order_id, $delete_scans = 1, $asp_id = false)
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
                'stage_type' => 'reg-docs',
                'params' => $order,
                'numeration' => (string)$key,
                'asp_id' => $order->asp,
                'hash' => sha1(rand(11111, 99999))
            ));
        }
    }

    private function action_cards_change()
    {
        $card_id = $this->request->post('card_id');
        $cards_name = $this->request->post('cards_name');
        $pan = $this->request->post('pan');
        $expdate = $this->request->post('expdate');

        $card =
            [
                'name' => $cards_name,
                'pan' => $pan,
                'expdate' => $expdate
            ];

        $this->cards->update_card($card_id, $card);
        exit;
    }

    private function action_accept_order_by_underwriter()
    {
        $order_id = $this->request->post('order_id');

        $users_docs = $this->Documents->get_documents(['order_id' => $order_id]);

        $order = $this->orders->get_order($order_id);

        $this->db->query("
        SELECT *
        FROM s_files
        WHERE user_id = ?
        ", $order->user_id);

        $photos = $this->db->results();
        $count_photos = 0;
        $count_approved_photos = 0;

        foreach ($photos as $photo) {
            if (in_array($photo->type, ['Паспорт: разворот', 'Паспорт: регистрация', 'Селфи с паспортом'])) {
                $count_photos++;

                if ($photo->status != 0)
                    $count_approved_photos++;
            }
        }

        if ($count_photos < 3) {
            echo json_encode(['error' => 'Не забудьте добавить фото документов и селфи с паспортом!']);
            exit;
        }

        if (empty($users_docs)) {
            echo json_encode(['error' => 'Не сформированы документы!']);
            exit;
        }
        if ($count_approved_photos < 3) {
            echo json_encode(['error' => 'Не забудьте подтвердить фото клиента!']);
            exit;
        }

        $query = $this->db->placehold("
        SELECT `type`
        FROM s_scans
        WHERE user_id = ?
        AND order_id = ?
        ", (int)$order->user_id, (int)$order->order_id);

        $this->db->query($query);
        $scans = $this->db->results();

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

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_accept_approve_by_under()
    {
        $order_id = $this->request->post('order_id');
        $manager_id = $this->request->post('manager_id');
        $order = $this->orders->get_order($order_id);

        $communication_theme = $this->CommunicationsThemes->get(12);

        $ticket =
            [
                'creator' => $manager_id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => $communication_theme->id,
                'company_id' => 3,
                'group_id' => 2,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $this->orders->update_order($order_id, ['status' => 10]);
        $this->tickets->update_by_theme_id(11, ['status' => 4], $order_id);
        exit;
    }

    private function add_first_ticket($order_id, $user_id)
    {
        $communication_theme = $this->CommunicationsThemes->get(18);
        $user = $this->users->get_user($user_id);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $user->lastname,
                'client_firstname' => $user->firstname,
                'client_patronymic' => $user->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => 18,
                'company_id' => 2,
                'group_id' => $user->group_id,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);
    }

    private function action_form_restruct_docs()
    {
        $order_id = $this->request->post('order_id');

        $order = $this->orders->get_order($order_id);

        $order->payment_schedule = (array)$this->PaymentsSchedules->get(['actual' => 1, 'order_id' => $order_id]);

        $payment_schedule = (array)$this->PaymentsSchedules->get(['actual' => 1, 'order_id' => $order_id]);
        $payment_schedule = json_decode($payment_schedule['schedule'], true);

        uksort($payment_schedule,
            function ($a, $b) {

                if ($a == $b)
                    return 0;

                if ($a == 'result' || $b == 'result')
                    return 1;

                return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
            });

        $change_employer = 0;

        foreach ($payment_schedule as $key => $schedule) {

            if ($key == 'result')
                break;

            if (isset($schedule['change_employer']))
                $change_employer = $schedule['change_employer'];

            $order->probably_return_date = $key;
        }

        $order->probably_return_date = date('Y-m-d', strtotime($order->probably_return_date));

        $this->documents->create_document(array(
            'user_id' => $order->user_id,
            'order_id' => $order->order_id,
            'stage_type' => 'restruct',
            'type' => 'DOP_SOGLASHENIE',
            'params' => $order,
            'numeration' => '04.03.3'
        ));

        $this->documents->create_document(array(
            'user_id' => $order->user_id,
            'order_id' => $order->order_id,
            'type' => 'DOP_GRAFIK',
            'stage_type' => 'restruct',
            'params' => $order,
            'numeration' => '04.04.1'
        ));
        $this->documents->create_document(array(
            'user_id' => $order->user_id,
            'order_id' => $order->order_id,
            'type' => 'OBSHIE_USLOVIYA_REST',
            'stage_type' => 'restruct',
            'params' => $order,
            'numeration' => '04.10'
        ));
        $this->documents->create_document(array(
            'user_id' => $order->user_id,
            'order_id' => $order->order_id,
            'type' => 'ZAYAVLENIE_RESTRUCT',
            'stage_type' => 'restruct',
            'params' => $order,
            'numeration' => '04.31'
        ));

        if ($change_employer == 1) {
            $this->documents->create_document(array(
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'type' => 'SOGLASIE_RUKRED_RABOTODATEL',
                'stage_type' => 'restruct',
                'params' => $order,
                'numeration' => '04.06'
            ));
            $this->documents->create_document(array(
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'type' => 'SOGLASIE_RABOTODATEL',
                'stage_type' => 'restruct',
                'params' => $order,
                'numeration' => '03.03'
            ));
            $this->documents->create_document(array(
                'user_id' => $order->user_id,
                'order_id' => $order->order_id,
                'type' => 'ZAYAVLENIE_ZP_V_SCHET_POGASHENIYA_MKR',
                'stage_type' => 'restruct',
                'params' => $order,
                'numeration' => '03.04'
            ));
        }

        $this->orders->update_order($order_id, ['probably_return_date' => $order->probably_return_date]);
        $this->PaymentsSchedules->update($order->payment_schedule->id, ['is_confirmed' => 1]);
    }

    private function action_reject_by_middle()
    {
        $order_id = $this->request->post('order_id');

        $order = $this->orders->get_order($order_id);

        $this->orders->update_order($order_id, ['status' => 11]);

        $this->tickets->update_by_theme_id(12, ['status' => 7], $order_id);

        $communication_theme = $this->CommunicationsThemes->get(47);

        $ticket =
            [
                'creator' => $this->manager->id,
                'creator_company' => 2,
                'client_lastname' => $order->lastname,
                'client_firstname' => $order->firstname,
                'client_patronymic' => $order->patronymic,
                'head' => $communication_theme->head,
                'text' => $communication_theme->text,
                'theme_id' => $communication_theme->id,
                'company_id' => $order->company_id,
                'group_id' => 2,
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

        $cron =
            [
                'ticket_id' => $ticket_id,
                'is_complited' => 0
            ];

        $this->NotificationsCron->add($cron);

        $cron =
            [
                'template_id' => 10,
                'user_id' => $order->user_id,
            ];

        $this->NotificationsClientsCron->add($cron);

        $this->eventlogs->add_log(array(
            'event_id' => 76,
            'manager_id' => $this->manager->id,
            'order_id' => $order_id,
            'user_id' => $order->user_id,
            'created' => date('Y-m-d H:i:s'),
        ));

        exit;
    }

    private function action_next_schedule_date()
    {
        $previous_date = $this->request->post('date');
        $order_id = $this->request->post('order');

        $schedule = $this->PaymentsSchedules->get(['actual' => 1, 'order_id' => $order_id]);
        $schedule = json_decode($schedule->schedule, true);
        array_shift($schedule);

        uksort($schedule,
            function ($a, $b) {

                if ($a == $b)
                    return 0;

                return (date('Y-m-d', strtotime($a)) < date('Y-m-d', strtotime($b))) ? -1 : 1;
            });


        foreach ($schedule as $date => $payment) {
            if (strtotime($previous_date) < strtotime($date) && !isset($next_pay)) {
                $next_pay = ['date' => $date, 'payment' => $payment];
            }
        }

        echo json_encode(['next_pay' => $next_pay]);
        exit;
    }

    private function add_month($date_str, $months)
    {
        $date = new DateTime($date_str);

        // We extract the day of the month as $start_day
        $start_day = $date->format('j');

        // We add 1 month to the given date
        $date->modify("+{$months} month");

        // We extract the day of the month again so we can compare
        $end_day = $date->format('j');

        if ($start_day != $end_day) {
            // The day of the month isn't the same anymore, so we correct the date
            $date->modify('last day of last month');
        }

        return $date;
    }

    private function action_get_companies()
    {
        $group_id = $this->request->post('group_id');

        $companies = $this->companies->get_companies(['group_id' => $group_id, 'blocked' => 0]);

        if (!empty($companies)) {
            $html = "<option value='0'>Выберите компанию</option>";

            foreach ($companies as $company) {
                $html .= "<option value='$company->id'>$company->name</option>";
            }

            echo json_encode(['html' => $html]);
            exit;
        } else {
            echo json_encode(['empty' => 1]);
            exit;
        }
    }

    private function action_get_branches()
    {
        $company_id = $this->request->post('company_id');

        $branches = $this->Branches->get_company_branches($company_id);

        if (!empty($branches)) {
            $html = '';
            foreach ($branches as $branch) {
                $html .= "<option value='$branch->id'>$branch->name</option>";
            }
            echo json_encode(['html' => $html]);
        } else {
            echo json_encode(['empty' => 1]);
        }
        exit;
    }

    private function action_bind_scan()
    {
        $doc_id = $this->request->post('document_id');
        $scan_id = $this->request->post('file_id');

        $this->documents->update_document($doc_id, ['scan_id' => $scan_id]);
        exit;
    }

    private function action_check_restruct_scans()
    {
        $order_id = $this->request->post('order_id');

        $documents = $this->documents->get_documents(['order_id' => $order_id, 'stage_type' => 'restruct']);

        $count_scans = 0;

        foreach ($documents as $document) {
            if (in_array($document->numeration, ['03.03', '03.04']) && !empty($document->scan_id))
                $count_scans++;
        }

        if ($count_scans < 2)
            echo json_encode(['error' => 'Не все сканы приложены']);
        else
            echo json_encode(['success' => 1]);

        exit;
    }

    private function action_personal_edit()
    {
        $order_id = $this->request->post('order_id');
        $user_id = $this->request->post('user_id');
        $comment = $this->request->post('comment');

        if (empty($comment)) {
            echo json_encode(['error' => 'Заполните комментарий!']);
            exit;
        }
        $Regadress = json_decode($this->request->post('Registration'));

        $regaddress = [];
        $regaddress['adressfull'] = $this->request->post('regaddress');
        $regaddress['zip'] = $Regadress->data->postal_code ?? '';
        $regaddress['region'] = $Regadress->data->region ?? '';
        $regaddress['region_type'] = $Regadress->data->region_type ?? '';
        $regaddress['city'] = $Regadress->data->city ?? '';
        $regaddress['city_type'] = $Regadress->data->city_type ?? '';
        $regaddress['district'] = $Regadress->data->city_district ?? '';
        $regaddress['district_type'] = $Regadress->data->city_district_type ?? '';
        $regaddress['locality'] = $Regadress->data->settlement ?? '';
        $regaddress['locality_type'] = $Regadress->data->settlement_type ?? '';
        $regaddress['street'] = $Regadress->data->street ?? '';
        $regaddress['street_type'] = $Regadress->data->street_type ?? '';
        $regaddress['house'] = $Regadress->data->house ?? '';
        $regaddress['building'] = $Regadress->data->block ?? '';
        $regaddress['room'] = $Regadress->data->flat ?? '';
        $regaddress['okato'] = $Regadress->data->okato ?? '';
        $regaddress['oktmo'] = $Regadress->data->oktmo ?? '';

        $Fakt_adress = json_decode($this->request->post('Faktadres'));

        $faktaddress = [];
        $faktaddress['adressfull'] = $this->request->post('faktaddress');
        $faktaddress['zip'] = $Fakt_adress->data->postal_code ?? '';
        $faktaddress['region'] = $Fakt_adress->data->region ?? '';
        $faktaddress['region_type'] = $Fakt_adress->data->region_type ?? '';
        $faktaddress['city'] = $Fakt_adress->data->city ?? '';
        $faktaddress['city_type'] = $Fakt_adress->data->city_type ?? '';
        $faktaddress['district'] = $Fakt_adress->data->city_district ?? '';
        $faktaddress['district_type'] = $Fakt_adress->data->city_district_type ?? '';
        $faktaddress['locality'] = $Fakt_adress->data->settlement ?? '';
        $faktaddress['locality_type'] = $Fakt_adress->data->settlement_type ?? '';
        $faktaddress['street'] = $Fakt_adress->data->street ?? '';
        $faktaddress['street_type'] = $Fakt_adress->data->street_type ?? '';
        $faktaddress['house'] = $Fakt_adress->data->house ?? '';
        $faktaddress['building'] = $Fakt_adress->data->block ?? '';
        $faktaddress['room'] = $Fakt_adress->data->flat ?? '';
        $faktaddress['okato'] = $Fakt_adress->data->okato ?? '';
        $faktaddress['oktmo'] = $Fakt_adress->data->oktmo ?? '';

        $old_user = $this->users->get_user($user_id);

        $lastname = trim($this->request->post('lastname'));
        $firstname = trim($this->request->post('firstname'));
        $patronymic = trim($this->request->post('patronymic'));
        $birth = trim($this->request->post('birth'));
        $birth_place = trim($this->request->post('birth_place'));
        $passport_serial = trim($this->request->post('passport_serial'));
        $passport_serial = str_replace('-', ' ', $passport_serial);
        $passport_date = trim($this->request->post('passport_date'));
        $subdivision_code = trim($this->request->post('subdivision_code'));
        $passport_issued = trim($this->request->post('passport_issued'));
        $inn = trim($this->request->post('inn'));
        $snils = trim($this->request->post('snils'));
        $personalNumber = trim($this->request->post('personal_number'));
        $projectNumber = trim($this->request->post('project_number'));

        $sameProjectNumber = ProjectContractNumberORM::where('uid', $projectNumber)
            ->where('orderId', '!=', $order_id)
            ->where('userId', '!=', $user_id)
            ->first();

        if (!empty($sameProjectNumber)) {
            echo json_encode(['error' => 'Такой проект номера уже есть']);
            exit;
        }

        $oldProjectNumber = ProjectContractNumberORM::where('orderId', $order_id)->where('userId', $user_id)->first();

        ProjectContractNumberORM::updateOrCreate(['orderId' => $order_id, 'userId' => $user_id], ['uid' => $projectNumber]);
        ContractsORM::where('order_id', $order_id)->update(['number' => $projectNumber]);

        $old_regaddress = $this->Addresses->get_address($old_user->regaddress_id);
        $old_faktaddress = $this->Addresses->get_address($old_user->faktaddress_id);

        AdressesORM::updateOrCreate(['id' => $old_user->regaddress_id], $regaddress);
        AdressesORM::updateOrCreate(['id' => $old_user->faktaddress_id], $faktaddress);

        $new_values = array(
            'Имя' => $lastname,
            'Фамилия' => $firstname,
            'Отчество' => $patronymic,
            'Дата рождения' => $birth,
            'Место рождения' => $birth_place,
            'Серия и номер паспорта' => $passport_serial,
            'Дата выдачи паспорта' => $passport_date,
            'Код подразделения' => $subdivision_code,
            'Кем выдан паспорт' => $passport_issued,
            'ИНН' => $inn,
            'СНИЛС' => $snils,
            'Адрес регистрации' => $old_user->regaddress_id,
            'Адрес проживания' => $old_user->faktaddress_id,
            'Персональный номер' => $personalNumber,
            'Проект номера' => $projectNumber,
            'Причина' => $comment
        );

        $old_values = array(
            'Имя' => $old_user->lastname,
            'Фамилия' => $old_user->firstname,
            'Отчество' => $old_user->patronymic,
            'Дата рождения' => $old_user->birth,
            'Место рождения' => $old_user->birth_place,
            'Серия и номер паспорта' => $old_user->passport_serial,
            'Дата выдачи паспорта' => $old_user->passport_date,
            'Код подразделения' => $old_user->subdivision_code,
            'Кем выдан паспорт' => $old_user->passport_issued,
            'ИНН' => $old_user->inn,
            'СНИЛС' => $old_user->snils,
            'Адрес регистрации' => empty($old_regaddress->adressfull) ?? '',
            'Адрес проживания' => empty($old_faktaddress->adressfull) ?? '',
            'Персональный номер' => $old_user->personal_number,
            'Проект номера' => $oldProjectNumber->uid
        );

        if ($old_user->lastname == $lastname) {
            unset($new_values['Имя']);
            unset($old_values['Имя']);
        }

        if (!empty($oldProjectNumber) && $oldProjectNumber == $projectNumber) {
            unset($new_values['Проект номера']);
            unset($old_values['Проект номера']);
        }

        if ($old_user->firstname == $firstname) {
            unset($new_values['Фамилия']);
            unset($old_values['Фамилия']);
        }

        if ($old_user->patronymic == $patronymic) {
            unset($new_values['Отчество']);
            unset($old_values['Отчество']);
        }

        if ($old_user->personal_number == $personalNumber) {
            unset($new_values['Персональный номер']);
            unset($old_values['Персональный номер']);
        }

        if (strtotime($old_user->birth) == strtotime($birth)) {
            unset($new_values['Дата рождения']);
            unset($old_values['Дата рождения']);
        }

        if ($old_user->birth_place == $birth_place) {
            unset($new_values['Место рождения']);
            unset($old_values['Место рождения']);
        }

        if ($old_user->passport_serial == $passport_serial) {
            unset($new_values['Серия и номер паспорта']);
            unset($old_values['Серия и номер паспорта']);
        }

        if (strtotime($old_user->passport_date) == strtotime($passport_date)) {
            unset($new_values['Дата выдачи паспорта']);
            unset($old_values['Дата выдачи паспорта']);
        }

        if ($old_user->subdivision_code == $subdivision_code) {
            unset($new_values['Код подразделения']);
            unset($old_values['Код подразделения']);
        }

        if ($old_user->passport_issued == $passport_issued) {
            unset($new_values['Кем выдан паспорт']);
            unset($old_values['Кем выдан паспорт']);
        }

        if ($old_user->inn == $inn) {
            unset($new_values['ИНН']);
            unset($old_values['ИНН']);
        }

        if ($old_user->snils == $snils) {
            unset($new_values['СНИЛС']);
            unset($old_values['СНИЛС']);
        }

        if (!empty($old_regaddress) && $old_regaddress->adressfull == $regaddress['adressfull']) {
            unset($new_values['Адрес регистрации']);
            unset($old_values['Адрес регистрации']);
        }

        if (!empty($old_faktaddress) && $old_faktaddress->adressfull == $faktaddress['adressfull']) {
            unset($new_values['Адрес проживания']);
            unset($old_values['Адрес проживания']);
        }

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'fio',
            'old_values' => serialize($old_values),
            'new_values' => serialize($new_values),
            'order_id' => $order_id,
            'user_id' => $user_id,
        ));

        $update = [];

        $keys =
            [
                'Имя' => 'lastname',
                'Фамилия' => 'firstname',
                'Отчество' => 'patronymic',
                'Дата рождения' => 'birth',
                'Место рождения' => 'birth_place',
                'Серия и номер паспорта' => 'passport_serial',
                'Дата выдачи паспорта' => 'passport_date',
                'Код подразделения' => 'subdivision_code',
                'Кем выдан паспорт' => 'passport_issued',
                'ИНН' => 'inn',
                'СНИЛС' => 'snils',
                'Персональный номер' => 'personal_number',
                'Адрес регистрации' => 'regaddress_id',
                'Адрес проживания' => 'faktaddress_id'
            ];

        foreach ($new_values as $key => $value) {
            if (isset($keys[$key])) {
                $update[$keys[$key]] = $value;
            }
        }

        if (!empty($update))
            UsersORM::where('id', $user_id)->update($update);

        $order = $this->orders->get_order($order_id);

        $order->payment_schedule = PaymentsScheduleORM::where('order_id', $order_id)->where('actual', 1)->first()->toArray();

        DocumentsORM::where('order_id', $order_id)
            ->update(['params' => serialize($order)]);

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_requisites_edit()
    {
        $userId = $this->request->post('user_id');
        $orderId = $this->request->post('order_id');
        $hold = $this->request->post('hold');
        $acc = $this->request->post('acc');
        $bank = $this->request->post('bank');
        $bik = $this->request->post('bik');
        $cor = $this->request->post('cor');
        $comment = $this->request->post('comment');

        if (empty($comment)) {
            echo json_encode(['error' => 'Заполните комментарий']);
        }

        $update =
            [
                'name' => $bank,
                'bik' => $bik,
                'number' => $acc,
                'holder' => $hold,
                'correspondent_acc' => $cor
            ];

        $oldRequisites = RequisitesORM::where('user_id', $userId)
            ->where('default', 1)
            ->first()->toArray();

        unset($oldRequisites['id']);
        unset($oldRequisites['default']);
        unset($oldRequisites['user_id']);

        $newValues = array_diff($update, $oldRequisites);
        $oldValues = array_intersect_key($oldRequisites, $newValues);

        RequisitesORM::where('user_id', $userId)->update($newValues);

        $translate =
            [
                'name' => "Наименование банка",
                'bik' => "Бик банка",
                'number' => "Номер счета",
                'holder' => "Держатель счета",
                'correspondent_acc' => "Кор счет"
            ];

        foreach ($oldValues as $key => $value) {
            $oldValues[$translate[$key]] = $value;
            unset($oldValues[$key]);
        }

        foreach ($newValues as $key => $value) {
            $newValues[$translate[$key]] = $value;
            unset($newValues[$key]);
        }

        $newValues['Причина'] = $comment;

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'requisites',
            'old_values' => serialize($oldValues),
            'new_values' => serialize($newValues),
            'order_id' => $orderId,
            'user_id' => $userId,
        ));

        $order = $this->orders->get_order($orderId);

        DocumentsORM::where('order_id', $orderId)
            ->update(['params' => serialize($order)]);

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_edit_loan_settings()
    {
        $orderId = (int)$this->request->post('order_id');
        $amount = (int)$this->request->post('amount');
        $userId = (int)$this->request->post('user_id');
        $loanTypeId = $this->request->post('loan_tarif');
        $probablyStartDate = $this->request->post('probably_start_date');
        $profUnion = $this->request->post('profunion');
        $groupId = $this->request->post('group');
        $companyId = $this->request->post('company');
        $brancheId = $this->request->post('branch');
        $comment = $this->request->post('comment');

        if (empty($comment)) {
            echo json_encode(['error' => 'Заполните комментарий']);
        }

        $branche = BranchesORM::find($brancheId);
        $loanType = LoantypesORM::find($loanTypeId);

        $probably_return_date = new DateTime(date('Y-m-' . $branche->payday, strtotime($probablyStartDate . '+' . $loanType->max_period . 'month')));
        $probably_return_date = $this->check_pay_date($probably_return_date);

        if ($amount < $loanType->min_amount || $amount > $loanType->max_amount) {
            echo json_encode(['error' => 'Проверьте сумму займа']);
            exit;
        }

        $groupLoanType = GroupLoanTypesORM::where('group_id', $groupId)->where('loantype_id', $loanTypeId)->first();

        if ($profUnion == 1) {
            $percent = (float)$groupLoanType->preferential_percents;
        } else {
            $percent = (float)$groupLoanType->standart_percents;
        }

        $order =
            [
                'amount' => $amount,
                'loan_type' => $loanTypeId,
                'percent' => $percent,
                'group_id' => $groupId,
                'company_id' => $companyId,
                'branche_id' => $brancheId,
                'probably_start_date' => date('Y-m-d', strtotime($probablyStartDate)),
            ];

        $user =
            [
                'group_id' => $groupId,
                'company_id' => $companyId,
                'branche_id' => $brancheId
            ];

        OrdersORM::where('id', $orderId)->update($order);
        UsersORM::where('id', $userId)->update($user);

        $this->action_reform_schedule($orderId);
        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_confirm_sms()
    {
        $code = $this->request->post('code');
        $orderId = $this->request->post('order');

        $order = OrdersORM::find($orderId);

        $this->db->query("
        SELECT code, created
        FROM s_sms_messages
        WHERE phone = ?
        AND code = ?
        AND user_id = ?
        ORDER BY created DESC
        LIMIT 1
        ", $order->user->phone_mobile, $code, $order->user->id);

        $results = $this->db->results();

        if (empty($results)) {

            echo json_encode(['error' => 1]);

        } else {
            $asp_log =
                [
                    'user_id' => $order->user->id,
                    'order_id' => $order->id,
                    'code' => $code,
                    'created' => date('Y-m-d H:i:s'),
                    'type' => 'sms',
                    'recepient' => $order->user->phone_mobile,
                    'manager_id' => $this->manager->id
                ];


            $asp_id = $this->AspCodes->add_code($asp_log);

            DocumentsORM::where('order_id', $orderId)
                ->whereNotIn('type', ['INDIVIDUALNIE_USLOVIA', 'GRAFIK_OBSL_MKR'])
                ->update(['asp_id' => $asp_id]);

            echo json_encode(['success' => 1]);
        }

        exit;
    }

    private function actionSendOnecTrigger()
    {
        $orderId = $this->request->post('orderId');
        $value = $this->request->post('value');

        OrdersORM::where('id', $orderId)->update(['canSendOnec' => $value]);
        exit;
    }

    private function actionSendYaDiskTrigger()
    {
        $orderId = $this->request->post('orderId');
        $value = $this->request->post('value');

        OrdersORM::where('id', $orderId)->update(['canSendYaDisk' => $value]);
        exit;
    }

    private function action_editPdn()
    {
        $orderId = $this->request->post('order_id');

        $order = OrdersORM::with('user')->find($orderId);

        $loanType = LoantypesORM::find($order->loan_type);

        $dependents = $this->request->post('dependents');

        $cards_bank_name = $this->request->post('cards_bank_name');
        $cards_limit = $this->request->post('cards_limit');
        $cards_rest_sum = $this->request->post('cards_rest_sum');
        $cards_validity_period = $this->request->post('cards_validity_period');
        $cards_delay = $this->request->post('cards_delay');

        $credits_bank_name = $this->request->post('credits_bank_name');
        $credits_rest_sum = $this->request->post('credits_rest_sum');
        $credits_month_pay = $this->request->post('credits_month_pay');
        $credits_return_date = $this->request->post('credits_return_date');
        $credits_percents = $this->request->post('credits_percents');
        $credits_delay = $this->request->post('credits_delay');

        $credits_story = array_replace_recursive($credits_bank_name, $credits_rest_sum, $credits_month_pay, $credits_return_date, $credits_percents, $credits_delay);
        $cards_story = array_replace_recursive($cards_bank_name, $cards_limit, $cards_rest_sum, $cards_validity_period, $cards_delay);

        $in = preg_replace("/[^,.0-9]/", '', $this->request->post('in'));
        $out = preg_replace("/[^,.0-9]/", '', $this->request->post('out'));

        if (empty($in)) {
            echo json_encode(['error' => 1, 'reason' => 'Отсутствует среднемесячный доход']);
            exit;
        }

        if (empty($out)) {
            echo json_encode(['error' => 1, 'reason' => 'Отсутствует среднемесячный расход']);
            exit;
        }

        $all_sum_credits = 0;
        $sum_credits_pay = 0;

        if (!empty($credits_story)) {
            foreach ($credits_story as $credit) {
                $credit['credits_month_pay'] = preg_replace("/[^,.0-9]/", '', $credit['credits_month_pay']);
                $credit['credits_rest_sum'] = preg_replace("/[^,.0-9]/", '', $credit['credits_rest_sum']);

                if (!empty($credit['credits_month_pay']) && $credit['credits_delay'] == 'Нет')
                    $sum_credits_pay += $credit['credits_month_pay'];

                if (!empty($credit['credits_rest_sum']) && $credit['credits_delay'] == 'Да')
                    $sum_credits_pay += $credit['credits_rest_sum'];
            }

            $all_sum_credits += $sum_credits_pay;
        }

        if (!empty($cards_story)) {
            foreach ($cards_story as $card) {
                $card['cards_rest_sum'] = preg_replace("/[^,.0-9]/", '', $card['cards_rest_sum']);
                $card['cards_limit'] = preg_replace("/[^,.0-9]/", '', $card['cards_limit']);

                if (!empty($card['cards_limit'])) {
                    $max = 0.05 * $card['cards_limit'];
                } else {
                    $max = 0;
                }
                if (!empty($card['cards_rest_sum'])) {
                    $min = 0.1 * $card['cards_rest_sum'];
                } else {
                    $min = 0;
                }

                $all_sum_credits += min($max, $min);
            }
        }

        $paymentsSchedule = PaymentsScheduleORM::where('order_id', $order->id)->where('actual', 1)->first();

        $month_pay = $order->amount * ((1 / $loanType->max_period) + (($paymentsSchedule->psk / 100) / 12));

        $all_sum_credits += $month_pay;

        if ($all_sum_credits != 0)
            $pdn = round(($all_sum_credits / $in) * 100, 2);
        else
            $pdn = 0;

        $update =
            [
                'pdn' => $pdn,
                'credits_story' => json_encode($credits_story),
                'cards_story' => json_encode($cards_story),
                'income' => $in,
                'expenses' => $out,
                'dependents' => $dependents
            ];

        UsersORM::where('id', $order->user_id)->update($update);

        echo json_encode(['success' => 1]);
        exit;
    }

}
