<?php

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
                    $response = $this->approve_order_action();
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


            endswitch;

        } else {
            $managers = array();
            foreach ($this->managers->get_managers() as $m)
                $managers[$m->id] = $m;

            $scoring_types = $this->scorings->get_types();

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scoring_types);echo '</pre><hr />';
            $this->design->assign('scoring_types', $scoring_types);

            if ($order_id = $this->request->get('id', 'integer')) {
                if ($order = $this->orders->get_order($order_id)) {

                    // сохраняем историю займов из 1с
                    $client = $this->users->get_user($order->user_id);

                    $credits_history = $this->soap1c->get_client_credits($client->UID);
                    $this->users->save_loan_history($client->id, $credits_history);

                    $client = $this->users->get_user($order->user_id);
                    $this->design->assign('client', $client);

                    $communications = $this->communications->get_communications(array('user_id' => $client->id));
                    $this->design->assign('communications', $communications);

                    // причина "не удалось выдать"
                    if ($order->status == 6) {
                        if ($p2p = $this->best2pay->get_contract_p2pcredit($order->contract_id)) {
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

                    $this->design->assign('order', $order);

                    $comments = $this->comments->get_comments(array('user_id' => $order->user_id));
                    foreach ($comments as $comment) {
                        if (isset($managers[$comment->manager_id]))
                            $comment->letter = mb_substr($managers[$comment->manager_id]->name, 0, 1);
                    }
                    $this->design->assign('comments', $comments);

                    $files = $this->users->get_files(array('user_id' => $order->user_id));
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
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scoring->body);echo '</pre><hr />';                            
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


                    // получаем комменты из 1С
                    $client = $this->users->get_user((int)$order->user_id);
                    if ($comments_1c_response = $this->soap1c->get_comments($client->UID)) {
                        $comments_1c = array();
                        if (!empty($comments_1c_response->Комментарии)) {
                            foreach ($comments_1c_response->Комментарии as $comm) {
                                $comment_1c_item = new StdClass();

                                $comment_1c_item->created = date('Y-m-d H:i:s', strtotime($comm->Дата));
                                $comment_1c_item->text = $comm->Комментарий;
                                $comment_1c_item->block = $comm->Блок;

                                $comments_1c[] = $comment_1c_item;
                            }
                        }

                        usort($comments_1c, function ($a, $b) {
                            return strtotime($b->created) - strtotime($a->created);
                        });

                        $this->design->assign('comments_1c', $comments_1c);

                        $blacklist_comments = array();
                        if (!empty($comments_1c_response->Комментарии)) {
                            foreach ($comments_1c_response->Комментарии as $comm) {
                                $blacklist_comment = new StdClass();

                                $blacklist_comment->created = date('Y-m-d H:i:s', strtotime($comm->Дата));
                                $blacklist_comment->text = $comm->Комментарий;
                                $blacklist_comment->block = $comm->Блок;

                                $blacklist_comments[] = $blacklist_comment;
                            }
                        }

                        usort($blacklist_comments, function ($a, $b) {
                            return strtotime($b->created) - strtotime($a->created);
                        });

                        $this->design->assign('blacklist_comments', $blacklist_comments);


                        $orders = array();
                        foreach ($this->orders->get_orders(array('user_id' => $order->user_id)) as $o) {
                            if (!empty($o->contract_id))
                                $o->contract = $this->contracts->get_contract($o->contract_id);

                            $orders[] = $o;
                        }
                        $this->design->assign('orders', $orders);

                        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($comments_1c_response);echo '</pre><hr />';
                    }

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
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scoring_types);echo '</pre><hr />';
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

            $this->Scans->delete_all_scans($order_id);

            $this->documents->delete_documents($order_id);

            $order = $this->orders->get_order($order_id);

            $doc_types =
                [
                    'DOP_SOGLASHENIE_K_TRUDOVOMU_DOGOVORU', 'SOGLASIE_MINB', 'SOGLASIE_NA_KRED_OTCHET', 'SOGLASIE_NA_OBR_PERS_DANNIH',
                    'SOGLASIE_RABOTODATEL', 'SOGLASIE_RDB', 'SOGLASIE_RUKRED_RABOTODATEL',
                    'ZAYAVLENIE_NA_PERECHISL_CHASTI_ZP', 'ZAYAVLENIE_ZP_V_SCHET_POGASHENIYA_MKR', 'INDIVIDUALNIE_USLOVIA'
                ];

            foreach ($doc_types as $type) {
                $results[$type] = $this->documents->create_document(array(
                    'user_id' => $order->user_id,
                    'order_id' => $order->order_id,
                    'contract_id' => ' ',
                    'type' => $type,
                    'params' => $order,
                ));
            }

            $filter['user_id'] = $order->user_id;

        }

        $order = $this->orders->get_order($order_id);

        $filter['user_id'] = $order->user_id;

        $documents = $this->documents->get_documents($filter);

        $scans = $this->Scans->get_scans_by_order_id($order_id);

        foreach ($documents as $document) {
            foreach ($scans as $scan) {
                if ($document->template == $scan->type)
                    $document->scan = $scan;
            }
        }

        $this->design->assign('documents', $documents);

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

    private function change_manager_action()
    {
        $order_id = $this->request->post('order_id', 'integer');
        $manager_id = $this->request->post('manager_id', 'integer');

        if (!($order = $this->orders->get_order((int)$order_id)))
            return array('error' => 'Неизвестный ордер');

        if (!in_array($this->manager->role, array('admin', 'developer', 'big_user')))
            return array('error' => 'Не хватает прав для выполнения операции', 'manager_id' => $order->manager_id);

        if (!empty($order->id_1c)) {
            $check_block = $this->soap1c->check_block_order_1c($order->id_1c);

            if ($check_block == 'Block_1c') {
                return array('error' => 'Заявка заблокирована в 1С', 'check_block' => $check_block);
            } elseif ($check_block == 'Block_CRM' && empty($manager_id)) {
                $this->soap1c->block_order_1c($order->id_1c, 0);
            } elseif ($check_block != 'Block_CRM' && !empty($manager_id)) {
                $this->soap1c->block_order_1c($order->id_1c, 1);
            }
        }

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

        if (!empty($order->id_1c)) {
            $check_block = $this->soap1c->check_block_order_1c($order->id_1c);

            if ($check_block == 'Block_1c') {
                return array('error' => 'Заявка заблокирована в 1С', 'check_block' => $check_block);
            } elseif ($check_block != 'Block_CRM') {
                $this->soap1c->block_order_1c($order->id_1c, 1);
            }
        }

        return array('success' => 1, 'status' => 1, 'manager' => $this->manager->name);
    }

    /**
     * OrderController::approve_order_action()
     * Одобрениие заявки
     * @return array
     */
    private function approve_order_action()
    {
        $order_id = $this->request->post('order_id', 'integer');

        if (!($order = $this->orders->get_order((int)$order_id)))
            return array('error' => 'Неизвестный ордер');

        if (!empty($order->manager_id) && $order->manager_id != $this->manager->id && !in_array($this->manager->role, array('admin', 'developer')))
            return array('error' => 'Не хватает прав для выполнения операции');

        if ($order->amount > 15000)
            return array('error' => 'Сумма займа должна быть не более 15000 руб!');

        if ($order->period > 14)
            return array('error' => 'Срок займа должен быть не более 14 дней!');

        if ($order->status != 1)
            return array('error' => 'Неверный статус заявки, возможно Заявка уже одобрена или получен отказ');

        if ($order->user_id == 127551)
            return array('error' => 'По данному клиенту запрещена выдача!');

        $update = array(
            'status' => 2,
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

        if (!empty($order->id_1c)) {
            $resp = $this->soap1c->block_order_1c($order->id_1c, 0);
        }

        // отправялем смс
        $msg = 'Активируй займ ' . ($order->amount * 1) . ' в личном кабинете, код ' . $accept_code . ' nalichnoeplus.ru/lk';
        $this->sms->send($order->phone_mobile, $msg);

//        ящик: sale@nalichnoeplus.com
//        заголовок: подтверждение выдачи
//        текст: текст - полное сообщение, как в смс. 
        $this->notify->email('sale@nalichnoeplus.com', 'Подтверждение выдачи', $msg);


        return array('success' => 1, 'status' => 2);

    }

    private function autoretry_accept_action()
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

                if (!empty($order->id_1c))
                    $resp = $this->soap1c->block_order_1c($order->id_1c, 0);

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

                    $resp = $this->soap1c->send_reject_reason($operation);

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

        if (!empty($order->id_1c)) {
            $resp = $this->soap1c->block_order_1c($order->id_1c, 0);
            $this->soap1c->send_order_status($order->id_1c, 'Отказано');
        }

        return array('success' => 1, 'status' => $status);
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

    private function action_cards()
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

    private function action_amount()
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

    private function contactdata_action()
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

    private function contacts_action()
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

    private function fio_action()
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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

        $order->status = $isset_order->status;
        $order->manager_id = $isset_order->manager_id;
        $order->phone_mobile = $isset_order->phone_mobile;

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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

        $this->design->assign('order', $order);

    }

    private function work_action()
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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

        $this->design->assign('order', $order);
    }

    private function action_passport()
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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

        $this->design->assign('order', $order);
    }

    private function reg_address_action()
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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

        $this->design->assign('order', $order);
    }

    private function fakt_address_action()
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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

        $this->design->assign('order', $order);
    }


    private function workdata_action()
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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

        $this->design->assign('order', $order);
    }


    private function work_address_action()
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

        $this->soap1c->update_fields($update, '', $isset_order->id_1c);

        $this->design->assign('order', $order);
    }

    private function socials_action()
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
        if (!empty($need_send)) {
            $send_resp = $this->soap1c->send_order_images($order->order_id, $need_send);
            if ($send_resp == 'OK')
                foreach ($need_send as $need_send_file)
                    $this->users->update_file($need_send_file->id, array('sent_1c' => 1, 'sent_date' => date('Y-m-d H:i:s')));
        }

        $this->design->assign('files', $files);
    }

    private function action_services()
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

    private function action_add_comment()
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

    private function action_close_contract()
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

    private function action_inn_change()
    {
        $user_id = (int)$this->request->post('user_id');
        $inn_number = $this->request->post('inn_number');

        $this->users->update_user($user_id, ['inn' => $inn_number]);
    }

    private function action_snils_change()
    {
        $user_id = (int)$this->request->post('user_id');
        $snils_number = $this->request->post('snils_number');

        $this->users->update_user($user_id, ['snils' => $snils_number]);
    }

    private function action_cors_change()
    {
        $user_id = (int)$this->request->post('user_id');
        $fio_acc_holder = $this->request->post('fio_acc_holder');
        $account_number = $this->request->post('account_number');
        $bank_name = $this->request->post('bank_name');
        $bik_bank = $this->request->post('bik_bank');

        $user =
            [
                'fio_acc_holder' => $fio_acc_holder,
                'account_number' => $account_number,
                'bank_name' => $bank_name,
                'bik_bank' => $bik_bank
            ];


        $this->users->update_user($user_id, $user);
    }


}