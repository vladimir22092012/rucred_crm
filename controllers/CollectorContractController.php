<?php

class CollectorContractController extends Controller
{
    public function fetch()
    {
    	if (!($contract_id = $this->request->get('id', 'integer')))
            return false;
        
        if ($this->request->method('post'))
        {
            switch ($this->request->post('action', 'string')):
            
                case 'add_notification':
                    $this->add_notification_action();
                break;
            
            endswitch;
        }
        
        $managers = array();
        foreach ($this->managers->get_managers() as $m)
            $managers[$m->id] = $m;

        $scoring_types = $this->scorings->get_types();
        $this->design->assign('scoring_types', $scoring_types);

        if ($contract = $this->contracts->get_contract($contract_id))
        {
            $code = $this->helpers->c2o_encode($contract->id);
            $short_link = $this->config->front_url.'/p/'.$code;

            $this->design->assign('short_link', $short_link);
            
            if ($order = $this->orders->get_order($contract->order_id))
            {
                $this->design->assign('order', $order);
                
                $comments = $this->comments->get_comments(array('order_id' => $contract->order_id));
                foreach ($comments as $comment)
                {
                    $comment->letter = mb_substr($managers[$comment->manager_id]->name, 0, 1);
                }
                $this->design->assign('comments', $comments);
                
                $files = $this->users->get_files(array('user_id'=>$order->user_id));
                $this->design->assign('files', $files);
                
                $documents = $this->documents->get_documents(array('order_id'=>$contract->order_id));
                $this->design->assign('documents', $documents);

                $communications = $this->communications->get_communications(array('user_id' => $order->user_id));
                $this->design->assign('communications', $communications);
                
                // входы в кабинет
                $authorizations = $this->authorizations->get_authorizations(array('user_id' => $order->user_id));
                $this->design->assign('authorizations', $authorizations);
                
                if (in_array('looker_link', $this->manager->permissions))
                {
                    $looker_link = $this->users->get_looker_link($order->user_id);
                    $this->design->assign('looker_link', $looker_link);
                }
    
    
                $contract_operations = $this->operations->get_operations(array('contract_id'=>$contract->id));
                $this->design->assign('contract_operations', $contract_operations);
            
                $this->design->assign('contract', $contract);
            
                $date1 = new DateTime(date('Y-m-d', strtotime($contract->return_date)));
                $date2 = new DateTime(date('Y-m-d'));
                
                $diff = $date2->diff($date1);
                $contract->delay = $diff->days;
    
                $contactpersons = $this->contactpersons->get_contactpersons(array('user_id' => $order->user_id));
                $this->design->assign('contactpersons', $contactpersons);
                
    
                $need_update_scorings = 0;
                $inactive_run_scorings = 0;
                $scorings = array();
                if ($result_scorings = $this->scorings->get_scorings(array('user_id'=>$order->user_id)))
                {
                    foreach ($result_scorings as $scoring)
                    {
                        if ($scoring->type == 'juicescore')
                        {
                            $scoring->body = unserialize($scoring->body);
    //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scoring->body);echo '</pre><hr />';                            
                        }
                        
                        if ($scoring->type == 'scorista')
                        {
                            $scoring->body = json_decode($scoring->body);
                            if (!empty($scoring->body->equifaxCH))
                                $scoring->body->equifaxCH = iconv('cp1251', 'utf8', base64_decode($scoring->body->equifaxCH));
                        }
                        if ($scoring->type == 'fssp')
                        {
                            $scoring->body = unserialize($scoring->body);
                            $scoring->found_46 = 0;
                            $scoring->found_47 = 0;
                            if (!empty($scoring->body->result[0]->result))
                            {
                                foreach ($scoring->body->result[0]->result as $result)
                                {
                                    if (!empty($result->ip_end))
                                    {
                                        $ip_end = array_map('trim', explode(',', $result->ip_end));
                                        if (in_array(46, $ip_end))
                                            $scoring->found_46 = 1;
                                        if (in_array(47, $ip_end))
                                            $scoring->found_47 = 1;
                                    }
                                }
                            }
                        }
                        
                        
                        $scorings[$scoring->type] = $scoring;
                    
                        if ($scoring->status == 'new' || $scoring->status == 'process')
                        {
                            $need_update_scorings = 1;
                            if (isset($scoring_types[$scoring->type]) && $scoring_types[$scoring->type]->type == 'first')
                                $inactive_run_scorings = 1;
                        }
                    }
                    
                    $scorings['efsrb'] = (object)array(
                        'success' => 1,
                        'string_result' => 'Проверка пройдена',
                        'status' => 'completed',
                        'created' => $scoring->created
                    );
                }
                $this->design->assign('scorings', $scorings);
                $this->design->assign('need_update_scorings', $need_update_scorings);
                $this->design->assign('inactive_run_scorings', $inactive_run_scorings);
    
    //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($scorings, $scoring_types);echo '</pre><hr />';
    
                $user = $this->users->get_user((int)$order->user_id);
                $changelogs = $this->changelogs->get_changelogs(array('order_id'=>$contract->order_id));
                foreach ($changelogs as $changelog)
                {
                    $changelog->user = $user;
                    if (!empty($changelog->manager_id) && !empty($managers[$changelog->manager_id]))
                        $changelog->manager = $managers[$changelog->manager_id];
                }
                $changelog_types = $this->changelogs->get_types();
                
                $this->design->assign('changelog_types', $changelog_types);
                $this->design->assign('changelogs', $changelogs);
                
                $cards = array();
                foreach ($this->cards->get_cards(array('user_id'=>$order->user_id)) as $card)
                    $cards[$card->id] = $card;
                $this->design->assign('cards', $cards);
    
                // получаем комменты из 1С
                $client = $this->users->get_user((int)$order->user_id);
                if ($comments_1c_response = $this->soap1c->get_comments($client->UID))
                {
                    $comments_1c = array();
                    if (!empty($comments_1c_response->Комментарии))
                    {
                        foreach ($comments_1c_response->Комментарии as $comm)
                        {
                            $comment_1c_item = new StdClass();
                            
                            $comment_1c_item->created = date('Y-m-d H:i:s', strtotime($comm->Дата));
                            $comment_1c_item->text = $comm->Комментарий;
                            $comment_1c_item->block = $comm->Блок;
                            
                            $comments_1c[] = $comment_1c_item;
                        }
                    }
                    
                    usort($comments_1c, function($a, $b){
                        return strtotime($b->created) - strtotime($a->created);
                    });
                    
                    $this->design->assign('comments_1c', $comments_1c);
                    
                    $blacklist_comments = array();
                    if (!empty($comments_1c_response->Комментарии))
                    {
                        foreach ($comments_1c_response->Комментарии as $comm)
                        {
                            $blacklist_comment = new StdClass();
                            
                            $blacklist_comment->created = date('Y-m-d H:i:s', strtotime($comm->Дата));
                            $blacklist_comment->text = $comm->Комментарий;
                            $blacklist_comment->block = $comm->Блок;
                            
                            $blacklist_comments[] = $blacklist_comment;
                        }
                    }
                    
                    usort($blacklist_comments, function($a, $b){
                        return strtotime($b->created) - strtotime($a->created);
                    });
                    
                    $this->design->assign('blacklist_comments', $blacklist_comments);
        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($comments_1c_response);echo '</pre><hr />';
                }
                
            }
            else
            {
                return false;
            }
        
            $collection_movings = $this->collections->get_movings(array('contract_id' => $contract->id));
            $this->design->assign('collection_movings', $collection_movings);
        }
        else
        {
            return false;
        }
        
        $collector_tags = array();
        foreach ($this->collector_tags->get_tags() as $ct)
            $collector_tags[$ct->id] = $ct;
        $this->design->assign('collector_tags', $collector_tags);
        
        $collection_statuses = $this->contracts->get_collection_statuses();
        $this->design->assign('collection_statuses', $collection_statuses);


        $notification_events = $this->notifications->get_events();
        $this->design->assign('notification_events', $notification_events);
        
        $default_notification_date = date('Y-m-d', time() + 86400 * 30);
        $this->design->assign('default_notification_date', $default_notification_date);
        
        if ($notifications = $this->notifications->get_notifications(array('collection_contract_id' => $contract->id)))
        {
            foreach ($notifications as $n)
            {
                if (!empty($n->event_id))
                    $n->event = $this->notifications->get_event($n->event_id);
            }
        }
        $this->design->assign('notifications', $notifications);

        
        return $this->design->fetch('collector_contract.tpl');
    }

    private function add_notification_action()
    {
        $notification = array(
            'collection_contract_id' => $this->request->post('contract_id', 'integer'),
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'notification_date' => date('Y-m-d', strtotime($this->request->post('notification_date'))),
            'comment' => $this->request->post('comment'),
            'event_id' => $this->request->post('event_id', 'integer')
        );
        
        if (empty($notification['event_id']))
        {
            $this->json_output(array('error' => 'Выберите событие'));
        }
        else
        {
            $id = $this->notifications->add_notification($notification);
        
            $this->json_output(array('success' => $id));
        }
    }
    
}