<?php
error_reporting(-1);
ini_set('display_errors', 'On');
class CollectorContractsController extends Controller
{
    public function fetch()
    {
        $items_per_page = 50;

        if ($this->request->method('post')) {
            switch ($this->request->post('action', 'string')) :
                case 'contactperson_comment':
                    $this->contactperson_comment_action();
                    break;
                
                case 'order_comment':
                    $this->order_comment_action();
                    break;
                
                case 'collection_manager':
                    $this->set_collection_manager_action();
                    break;
                
                case 'collection_status':
                    $this->set_collection_status_action();
                    break;
                
                case 'workout':
                    $this->set_workout_action();
                    break;
                
                case 'hide_prolongation':
                    $this->set_hide_prolongation_action();
                    break;
                
                case 'sud_label':
                    $this->set_sud_label_action();
                    break;
                
                case 'send_sms':
                    $this->send_sms_action();
                    break;
                
                case 'distribute':
                    $this->distribute_action();
                    break;
            endswitch;
        }
        
        $order_ids = array();
        $user_ids = array();
        $contracts = array();
        
        $filter = array();
        
        $sms_templates = $this->sms->get_templates(array('type' => 'collection'));
        $this->design->assign('sms_templates', $sms_templates);
        
        if (!($period = $this->request->get('period'))) {
            $period = 'all';
        }

        switch ($period) :
            case 'month':
                $filter['inssuance_date_from'] = date('Y-m-01');
                break;
             
            case 'year':
                $filter['inssuance_date_from'] = date('Y-01-01');
                break;
             
            case 'all':
                $filter['inssuance_date_from'] = null;
                $filter['inssuance_date_to'] = null;
                break;
             
            case 'optional':
                $daterange = $this->request->get('daterange');
                $filter_daterange = array_map('trim', explode('-', $daterange));
                $filter['inssuance_date_from'] = date('Y-m-d', strtotime($filter_daterange[0]));
                $filter['inssuance_date_to'] = date('Y-m-d', strtotime($filter_daterange[1]));
                break;
        endswitch;
        $this->design->assign('period', $period);
        
        if (empty($filter['inssuance_date_from']) || strtotime($filter['inssuance_date_from']) < strtotime('2021-06-01')) {
            $filter['inssuance_date_from'] = '2021-06-01';
        }


        
        if ($search = $this->request->get('search')) {
            $filter['search'] = array_filter($search);
            $this->design->assign('search', array_filter($search));
        }
        
        $filter['type'] = 'base';
        
        
        if ($this->manager->role == 'collector') {
            $filter['collection_status'] = array($this->manager->collection_status_id);
            $filter['collection_manager_id'] = $this->manager->id;
        } elseif (in_array($this->manager->role, array('developer', 'admin', 'chief_collector'))) {
            $filter['collection_status'] = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
            $filter['collection_manager_id'] = null;
        } elseif ($this->manager->role == 'team_collector') {
            $filter['collection_status'] = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
            $filter['collection_manager_id'] = $this->manager->team_id;
        }
        
        if ($filter_status = $this->request->get('status', 'integer')) {
            $filter['collection_status'] = array($filter_status);
        }
        $this->design->assign('filter_status', $filter_status);
            
        if (!($sort = $this->request->get('sort'))) {
            $sort = 'order_id_asc';
        }
        $this->design->assign('sort', $sort);
        $filter['sort'] = $sort;
        
        
        if ($page_count = $this->request->get('page_count')) {
            setcookie('page_count', $page_count, time()+86400*30, '/');
            if ($page_count == 'all') {
                $items_per_page = 10000;
            } else {
                $items_per_page = $page_count;
            }
                
            $this->design->assign('page_count', $page_count);
        } elseif (!empty($_COOKIE['page_count'])) {
            if ($_COOKIE['page_count'] == 'all') {
                $items_per_page = 10000;
            } else {
                $items_per_page = $_COOKIE['page_count'];
            }
        
            $this->design->assign('page_count', $_COOKIE['page_count']);
        }


        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);
        $this->design->assign('items_per_page', $items_per_page);

        $contracts_count = $this->contracts->count_contracts($filter);
        
        $pages_num = ceil($contracts_count/$items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $contracts_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;
        
        $filter['sort_workout'] = 1;
        
        foreach ($this->contracts->get_contracts($filter) as $con) {
            $order_ids[] = $con->order_id;
            $user_ids[] = $con->user_id;
            
            $date1 = new DateTime(date('Y-m-d', strtotime($con->return_date)));
            $date2 = new DateTime(date('Y-m-d'));
                
            $diff = $date2->diff($date1);
            $con->delay = $diff->days;
                                    
            $contracts[$con->id] = $con;
        }

        if (!empty($contracts)) {
            $contactpersons = array();
            foreach ($this->contactpersons->get_contactpersons(array('user_id' => $user_ids)) as $cp) {
                if (!isset($contactpersons[$cp->user_id])) {
                    $contactpersons[$cp->user_id] = array();
                }
                $contactpersons[$cp->user_id][] = $cp;
            }
            
            $comments = array();
            foreach ($this->comments->get_comments(array('order_id' => $order_ids)) as $com) {
                if (empty($com->contactperson_id)) {
                    if (!isset($comments[$com->order_id])) {
                        $comments[$com->order_id] = array();
                    }
                    $comments[$com->order_id][] = $com;
                }
            }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($comments);echo '</pre><hr />';
            $orders = array();
            foreach ($this->orders->get_orders(array('id' => $order_ids)) as $o) {
                if (isset($comments[$o->order_id])) {
                    $o->comments = $comments[$o->order_id];
                }
                
                $orders[$o->order_id] = $o;
//                if (isset($contracts[$o->contract_id]))
//                    $contracts[$o->contract_id]->order = $o;
            }
            
            
            foreach ($contracts as $contract) {
                if (isset($orders[$contract->order_id])) {
                    $contract->order = $orders[$contract->order_id];
                }
                
                if (!empty($contactpersons[$contract->user_id])) {
                    $contract->contactpersons = $contactpersons[$contract->user_id];
                } else {
                    if (!empty($contract->order->contact_person_name) && !empty($contract->order->contact_person_phone)) {
                        $new_contactperson = array(
                            'user_id' => $contract->user_id,
                            'name' => $contract->order->contact_person_name,
                            'relation' => $contract->order->contact_person_relation,
                            'phone' => $contract->order->contact_person_phone,
                        );
                        $new_contactperson['id'] = $this->contactpersons->add_contactperson($new_contactperson);
                        
                        $contract->contactpersons[] = (object)$new_contactperson;
                    }
                    
                    if (!empty($contract->order->contact_person2_name) && !empty($contract->order->contact_person2_phone)) {
                        $new_contactperson2 = array(
                            'user_id' => $contract->user_id,
                            'name' => $contract->order->contact_person2_name,
                            'relation' => $contract->order->contact_person2_relation,
                            'phone' => $contract->order->contact_person2_phone,
                        );
                        $new_contactperson2['id'] = $this->contactpersons->add_contactperson($new_contactperson2);
                        
                        $contract->contactpersons[] = (object)$new_contactperson2;
                    }
                }
                
                if (!empty($contract->order)) {
                    $contract->client_time = $this->helpers->get_regional_time($contract->order->Regregion);
                } else {
                    $contract->client_time = date('Y-m-d H:i:s');
                }
                $contract->client_time_warning = $this->users->get_time_warning($contract->client_time);
            }
            /*
            usort($contracts, function($a, $b){
                 if (empty($a->collection_workout) && empty($b->collection_workout))
                    return 0;
                 if (!empty($a->collection_workout) && !empty($b->collection_workout))
                    return 0;
                 if (empty($a->collection_workout) && !empty($b->collection_workout))
                    return -1;
                 if (!empty($a->collection_workout) && empty($b->collection_workout))
                    return 1;
            });
            */
            
            $this->design->assign('contracts', $contracts);
        }
        
        $collection_statuses = $this->contracts->get_collection_statuses();
        $this->design->assign('collection_statuses', $collection_statuses);
        
        $collector_tags = array();
        foreach ($this->collector_tags->get_tags() as $ct) {
            $collector_tags[$ct->id] = $ct;
        }
        $this->design->assign('collector_tags', $collector_tags);
        
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($collection_statuses);echo '</pre><hr />';
        
        return $this->design->fetch('collector_contracts.tpl');
    }
    
    private function set_collection_manager_action()
    {
        $contract_id = $this->request->post('contract_id', 'integer');
        $manager_id = $this->request->post('manager_id', 'integer');
        
        $this->contracts->update_contract($contract_id, array('collection_manager_id' => $manager_id));

        $current_contract = $this->contracts->get_contract($contract_id);

        $this->contracts->update_contract($contract_id, array(
            'collection_manager_id' => $manager_id,
            'collection_handchange' => 0,
            'collection_workout' => 0,
            'collection_tag' => '',
        ));
        
        $this->users->update_user($current_contract->user_id, array('contact_status' => 0));

        $this->collections->add_moving(array(
            'initiator_id' => (int)$this->manager->id,
            'manager_id' => $manager_id,
            'contract_id' => $current_contract->id,
            'from_date' => date('Y-m-d H:i:s'),
            'summ_body' => $current_contract->loan_body_summ,
            'summ_percents' => $current_contract->loan_percents_summ + $current_contract->loan_peni_summ + $current_contract->loan_charge_summ,
        ));
        
        $this->json_output(array('success' => 1));
        exit;
    }
    
    private function set_workout_action()
    {
        $contract_id = $this->request->post('contract_id', 'integer');
        $workout = $this->request->post('workout', 'integer');
    
        $res = $this->contracts->update_contract($contract_id, array('collection_workout'=>$workout));

        $this->json_output(array('success' => $res));
        exit;
    }
    
    private function set_hide_prolongation_action()
    {
        $contract_id = $this->request->post('contract_id', 'integer');
        $hide_prolongation = $this->request->post('hide_prolongation', 'integer');
    
        $res = $this->contracts->update_contract($contract_id, array('hide_prolongation'=>$hide_prolongation));

        $this->json_output(array('success' => $res));
        exit;
    }
    
    private function set_sud_label_action()
    {
        $sud = $this->request->post('sud', 'integer');
        $contract_id = $this->request->post('contract_id', 'integer');
        
        $old_contract = $this->contracts->get_contract((int)$contract_id);

        $this->contracts->update_contract($contract_id, array('sud' => $sud));

        if (!empty($sud)) {
            $user = $this->users->get_user($old_contract->user_id);
            $sudblock_contract = array(
                'number' => $old_contract->number,
                'first_number' => $old_contract->number,
                'user_id' => $old_contract->user_id,
                'contract_id' => $old_contract->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'patronymic' => $user->patronymic,
                'created' => date('Y-m-d H:i:s'),
                'status' => 1,
                'loan_summ' => $old_contract->loan_body_summ,
                'total_summ' => $old_contract->loan_body_summ + $old_contract->loan_percents_summ + $old_contract->loan_charge_summ + $old_contract->loan_peni_summ,
                'region' => trim($user->Regregion.' '.$user->Regregion_shorttype),
                'provider' => 'Наличное плюс',
            );
            if ($tribunal = $this->tribunals->find_tribunal($user->Regregion)) {
                $sudblock_contract['tribunal'] = $tribunal->sud;
            }
            
            $this->sudblock->add_contract($sudblock_contract);
        }


        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'collection_status',
            'old_values' => serialize(array(
                'sud' => $old_contract->sud,
            )),
            'new_values' => serialize(array(
                'sud' => $sud,
            )),
            'order_id' => $old_contract->order_id,
            'user_id' => $old_contract->user_id,
        ));

        $this->json_output(array('success' => 1));
        exit;
    }
    
    private function set_collection_status_action()
    {
        $contract_id = $this->request->post('contract_id', 'integer');
        $status_id = $this->request->post('status_id', 'integer');
        
        $old_contract = $this->contracts->get_contract((int)$contract_id);
        
        $this->contracts->update_contract($contract_id, array('collection_status' => $status_id, 'collection_handchange'=>1));

        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'collection_status',
            'old_values' => serialize(array(
                'collection_status' => $old_contract->collection_status,
                'collection_handchange' => $old_contract->collection_handchange
            )),
            'new_values' => serialize(array(
                'collection_status' => $status_id,
                'collection_handchange' => 1
            )),
            'order_id' => $old_contract->order_id,
            'user_id' => $old_contract->user_id,
        ));
    
    
        $this->json_output(array('success' => 1));
        exit;
    }
    
    private function contactperson_comment_action()
    {
        $comment = trim($this->request->post('text'));
        $contactperson_id = $this->request->post('contactperson_id', 'integer');
        $order_id = $this->request->post('order_id', 'integer');
        
        if ($contactperson = $this->contactpersons->get_contactperson($contactperson_id)) {
            if (!empty($comment)) {
                $this->contactpersons->update_contactperson($contactperson_id, array('comment' => $comment));
                $this->comments->add_comment(array(
                    'order_id' => $order_id,
                    'user_id' => $contactperson->user_id,
                    'contactperson_id' => $contactperson_id,
                    'manager_id' => $this->manager->id,
                    'text' => $comment,
                    'created' => date('Y-m-d H:i:s'),
                    'sent' => 0,
                    'status' => 0,
                ));
                $this->json_output(array('success' => 1));
            } else {
                $this->json_output(array('error' => 'Напишите комментарий'));
            }
        } else {
            $this->json_output(array('error' => 'Контакное лицо не найдено'));
        }
        exit;
    }
    
    private function order_comment_action()
    {
        $comment = trim($this->request->post('text'));
        $order_id = $this->request->post('order_id', 'integer');
        
        if ($order = $this->orders->get_order($order_id)) {
            if (!empty($comment)) {
                $this->comments->add_comment(array(
                    'order_id' => $order_id,
                    'user_id' => $order->user_id,
                    'contactperson_id' => 0,
                    'manager_id' => $this->manager->id,
                    'text' => $comment,
                    'created' => date('Y-m-d H:i:s'),
                    'sent' => 0,
                    'status' => 0,
                ));
                $this->json_output(array('success' => 1));
            } else {
                $this->json_output(array('error' => 'Напишите комментарий'));
            }
        } else {
            $this->json_output(array('error' => 'Договор не найден'));
        }
        exit;
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
                $payment_link = $this->config->front_url.'/p/'.$code;
                $template->template = str_replace('{$payment_link}', $payment_link, $template->template);
            }
        }
                        
        $resp = $this->sms->send(
            /*'79276928586'*/            $user->phone_mobile,
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
            'text' => 'Клиенту отправлено смс с текстом: '.$template->template,
            'created' => date('Y-m-d H:i:s'),
            'organization' => empty($yuk) ? 'mkk' : 'yuk',
            'auto' => 1
        ));
        
        $this->changelogs->add_changelog(array(
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'send_sms',
            'old_values' => '',
            'new_values' => $template->template,
            'user_id' => $user->id,
            'order_id' => $order_id,
        ));
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
        $this->json_output(array('success'=>true));
    }

    private function distribute_action()
    {
        $managers = $this->request->post('managers');
        $contracts = $this->request->post('contracts');
        $type = $this->request->post('type');
        
        $all_managers = array();
        foreach ($this->managers->get_managers() as $m) {
            $all_managers[$m->id] = $m;
        }
        
        if (empty($managers)) {
            $this->json_output(array('error' => 'Нет пользователей для распределения'));
        } elseif (empty($contracts) && $type != 'optional') {
            $this->json_output(array('error' => 'Нет договоров для распределения'));
        } else {
            switch ($type) :
                case 'checked':
                case 'all':
                    $distribute = array();
                    $i = 0;
                    $count_managers = count($managers);
                    foreach ($contracts as $contract_id) {
                        $distribute[$contract_id] = $managers[$i];
                        
                        $this->contracts->update_contract($contract_id, array(
                            'collection_manager_id' => $managers[$i],
                            'collection_handchange' => 0,
                            'collection_workout' => 0,
                            'collection_tag' => '',
                            'collection_status' => $all_managers[$managers[$i]]->collection_status_id,
                        ));
                        
                        $contract = $this->contracts->get_contract($contract_id);

                        $this->users->update_user($contract->user_id, array('contact_status' => 0));
                        
                        $this->collections->add_moving(array(
                            'initiator_id' => (int)$this->manager->id,
                            'manager_id' => $managers[$i],
                            'contract_id' => $contract->id,
                            'from_date' => date('Y-m-d H:i:s'),
                            'summ_body' => $contract->loan_body_summ,
                            'summ_percents' => $contract->loan_peni_summ + $contract->loan_percents_summ + $contract->loan_charge_summ,
                        ));

                        $i++;
                        if ($i == $count_managers) {
                            $i = 0;
                        }
                    }

                    break;
            
                case 'optional':
                    if (!($period = $this->request->get('period'))) {
                        $period = 'all';
                    }
            
                    switch ($period) :
                        case 'month':
                            $filter['inssuance_date_from'] = date('Y-m-01');
                            break;
                         
                        case 'year':
                            $filter['inssuance_date_from'] = date('Y-01-01');
                            break;
                         
                        case 'all':
                            $filter['inssuance_date_from'] = null;
                            $filter['inssuance_date_to'] = null;
                            break;
                         
                        case 'optional':
                            $daterange = $this->request->get('daterange');
                            $filter_daterange = array_map('trim', explode('-', $daterange));
                            $filter['inssuance_date_from'] = date('Y-m-d', strtotime($filter_daterange[0]));
                            $filter['inssuance_date_to'] = date('Y-m-d', strtotime($filter_daterange[1]));
                            break;
                    endswitch;
                    
                    if (empty($filter['inssuance_date_from']) || (strtotime($filter['inssuance_date_from']) < strtotime('2021-06-01 00:00:00'))) {
                            $filter['inssuance_date_from'] = '2021-06-01';
                    }
                    
                    if ($search = $this->request->get('search')) {
                        $filter['search'] = array_filter($search);
                    }
                    
                    $filter['type'] = 'base';
                    
                    
                    if ($this->manager->role == 'collector') {
                        $filter['collection_status'] = array($this->manager->collection_status_id);
                        $filter['collection_manager_id'] = $this->manager->id;
                    } elseif (in_array($this->manager->role, array('developer', 'admin', 'chief_collector'))) {
                        $filter['collection_status'] = array(1,2,3,4,5,6, 7, 8, 9);
                        $filter['collection_manager_id'] = null;
                    } elseif ($this->manager->role == 'team_collector') {
                        $filter['collection_status'] = array(1,2,3,4,5,6, 7, 8, 9);
                        $filter['collection_manager_id'] = $this->manager->team_id;
                    }
                    
                    if ($filter_status = $this->request->get('status', 'integer')) {
                        $filter['collection_status'] = array($filter_status);
                    }
                        
                    $filter['sort'] = 'total_desc';
                    $filter['limit'] = 10000;
                    
                    foreach ($this->contracts->get_contracts($filter) as $con) {
                        $contracts[] = $con;
                    }
        
                    $contracts_count = count($contracts);
                    $quantity = $this->request->post('quantity');
                    
                    if ($contracts_count > $quantity) {
                        $coef = $contracts_count / $quantity;
                    } else {
                        $coef = 1;
                    }
                    
                    $reset = 1;
                    $prepare_contracts = array();
                    $summ_coef = 0;
                    while (count($prepare_contracts) < $quantity) {
                        $current_index = intval($summ_coef);
                        $prepare_contracts[$current_index] = $contracts[$current_index];
                        
                        $summ_coef += $coef;
                    
                        if (intval($summ_coef) > $contracts_count - 1) {
                            $summ_coef = $reset;
                            $reset++;
                            if ($reset > 2) {
                                $summ_coef = 0;
                                $coef = 1;
                            }
                        }
                    }
                    
                    
                    $distribute = array();
                    $i = 0;
                    $count_managers = count($managers);
                    foreach ($prepare_contracts as $contract) {
                        $distribute[$contract->id] = $managers[$i];
                        
                        $this->contracts->update_contract($contract->id, array(
                            'collection_manager_id' => $managers[$i],
                            'collection_workout' => 0,
                            'collection_handchange' => 0,
                            'collection_tag' => '',
                            'collection_status' => $all_managers[$managers[$i]]->collection_status_id,
                        ));
                        
                        $this->users->update_user($contract->user_id, array('contact_status' => 0));
                        
                        $this->collections->add_moving(array(
                            'manager_id' => $managers[$i],
                            'contract_id' => $contract->id,
                            'from_date' => date('Y-m-d H:i:s'),
                            'summ_body' => $contract->loan_body_summ,
                            'summ_percents' => $contract->loan_peni_summ + $contract->loan_percents_summ + $contract->loan_charge_summ,
                        ));
                        

                        $i++;
                        if ($i == $count_managers) {
                            $i = 0;
                        }
                    }

                    
                    break;
            endswitch;
            








            $this->json_output(array('success' => '1', 'distribute' => $distribute));
        }
    }
    
    private function get_current_manager()
    {
    }
}
