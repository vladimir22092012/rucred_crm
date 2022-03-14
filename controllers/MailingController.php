<?php

class MailingController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post'))
        {
            $collectors = (array)$this->request->post('collectors');
            
            $sms = $this->request->post('sms', 'integer');
            $zvonobot = $this->request->post('zvonobot', 'integer');
            
            $mkk_check_number = $this->request->post('mkk_check_number');
            $yuk_check_number = $this->request->post('yuk_check_number');

            $mkk_text = $this->request->post('mkk_text');
            $yuk_text = $this->request->post('yuk_text');
            
            $this->design->assign('collectors', $collectors);
            $this->design->assign('sms', $sms);
            $this->design->assign('zvonobot', $zvonobot);
            $this->design->assign('mkk_check_number', $mkk_check_number);
            $this->design->assign('yuk_check_number', $yuk_check_number);
            $this->design->assign('mkk_text', $mkk_text);
            $this->design->assign('yuk_text', $yuk_text);
            
            
            $errors = array();
            
//            if (empty($collectors))
//                $errors[] = 'Выберите коллекторов для рассылки';
            
            if (empty($sms) && empty($zvonobot))
                $errors[] = 'Выберите тип рассылки (звонобот или смс-рассылка)';
            
            if (empty($mkk_text) && empty($yuk_text))
                $errors[] = 'Введите шаблоны сообщений';
            
            $this->design->assign('errors', $errors);
            
            if (empty($errors))
            {
                $mkk_numbers = array();
                $yuk_numbers = array();
                
                if (!empty($mkk_check_number))
                {
                    if (!empty($sms))
                        $this->sms->send($mkk_check_number, $mkk_text, 0);
                    
                    if (!empty($zvonobot))
                    {
                        $name = 'mkk_mailing_'.date('ymd');
                    	$record_id = $this->zvonobot->create_record($name, $mkk_text, 0);
                        $this->zvonobot->call($mkk_check_number, $record_id['data']['id'], 0);                
                    }

                }
                if (!empty($yuk_check_number))
                {
                    if (!empty($sms))
                        $this->sms->send($yuk_check_number, $yuk_text, 1);
                    
                    if (!empty($zvonobot))
                    {
                        $name = 'yuk_mailing_'.date('ymd');
                    	$record_id = $this->zvonobot->create_record($name, $yuk_text, 1);
                        $this->zvonobot->call($yuk_check_number, $record_id['data']['id'], 1);                
                    }

                }
                
                if (!empty($collectors))
                {
                    $add_mailing = array(
                        'manager_id' => $this->manager->id,
                        'created' => date('Y-m-d H:i:s'),
                        'sms' => $sms,
                        'zvonobot' => $zvonobot,
                        'mkk_check_number' => $mkk_check_number,
                        'mkk_text' => $mkk_text,
                        'yuk_check_number' => $yuk_check_number,
                        'yuk_text' => $yuk_text,
                        'collectors' => serialize($collectors),
                    );
                    $mailing_id = $this->mailings->add_mailing($add_mailing);
                    
                    $total_count = 0;
                    
                    if ($contracts = $this->contracts->get_contracts(array('collection_manager_id' => $collectors, 'status' => array(4,7))))
                    {
                        foreach ($contracts as $c)
                        {
                            if (empty($c->premier))
                            {
                                $user = $this->users->get_user($c->user_id);
                                
                                $need_send = 0;
                                
                                if (empty($c->sold) && !empty($mkk_text))
                                {
                                    $text = $mkk_text;
                                    $yuk = 0;  
                                    $need_send = 1;                              
                                }
                                if (!empty($c->sold) && !empty($yuk_text))
                                {
                                    $text = $yuk_text;
                                    $yuk = 1;
                                    $need_send = 1;
                                }
                                
                                if (!empty($need_send))
                                {
                                    $add_mail_item = array(
                                        'mailing_id' => $mailing_id,
                                        'manager_id' => $this->manager->id,
                                        'user_id' => $c->user_id,
                                        'status' => 1,
                                        'created' => date('Y-m-d H:i:s'),
                                        'phone' => $user->phone_mobile,
                                        'text' => $text,
                                        'yuk' => $yuk,
                                    );
                                    
                                    if (!empty($sms))
                                    {
                                        $add_mail_item['type'] = 'sms';
                                        $this->mailings->add_item($add_mail_item);
                                    
                                        $total_count++;
                                    }
                                    
                                    if (!empty($zvonobot))
                                    {
                                        $add_mail_item['type'] = 'zvonobot';
                                        $this->mailings->add_item($add_mail_item);
                                    
                                        $total_count++;
                                    }
                                }
                            }
                        }
                        
                    }
                    
                    $this->mailings->update_mailing($mailing_id, array('total_mail'=>$total_count));
                }
                

                $this->design->assign('success', 1);
                
            }

            
        }
        else
        {
            switch ($this->request->get('action')):
                
                case 'list':
                    return $this->action_list();
                break;
                
                case 'new':
                    return $this->action_new();
                break;
                
                case 'calc':
                    return $this->action_calc();
                break;
                
            endswitch;
        }
        
        
        
        
        
        
        $collectors = array();
        foreach ($this->managers->get_managers() as $m)
        {
            if ($m->role == 'collector' && empty($m->blocked))
            {
                if (!isset($collectors[$m->collection_status_id]))
                    $collectors[$m->collection_status_id] = array();

                if ($this->manager->role == 'team_collector')
                {
                    if (in_array($m->id, (array)$this->manager->team_id))
                        $collectors[$m->collection_status_id][] = $m;
                }
                else
                {
                    $collectors[$m->collection_status_id][] = $m;
                }
            }
        }
        $collectors = array_filter($collectors);
        
        $this->design->assign('collectors', $collectors);
/*
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($collectors);echo '</pre><hr />';        
        $team_collectors = array();
        $managers = array();
        foreach ($this->managers->get_managers() as $m)
        {
            if ($m->role == 'team_collector')
            {
                $team_collectors[$m->id] = $m;
                $team_collectors[$m->id]->collectors = array();
            }
            $managers[$m->id] = $m;
        }
        foreach ($managers as $m)
        {
            if ($m->role == 'collector')
                $team_collectors[$m->team_id]->collectors[] = $m;
            
        }
        
        if ($this->manager->role == 'team_collector')
        {
            $team_collectors = array($this->manager->id => $team_collectors[$this->manager->id]);
        }
        
        $this->design->assign('team_collectors', $team_collectors);
*/        
        $collection_statuses = $this->contracts->get_collection_statuses();
        $this->design->assign('collection_statuses', $collection_statuses);
        
    	return $this->design->fetch('mailing_new.tpl');
    }
    
    private function action_list()
    {
        $items_per_page = 20;

    	$filter = array();

        if (!($sort = $this->request->get('sort', 'string')))
        {
            $sort = 'date_desc';
        }
        $filter['sort'] = $sort;
        $this->design->assign('sort', $sort);

		$current_page = $this->request->get('page', 'integer');
		$current_page = max(1, $current_page);
		$this->design->assign('current_page_num', $current_page);

		$clients_count = $this->mailings->count_mailings($filter);
		
		$pages_num = ceil($clients_count/$items_per_page);
		$this->design->assign('total_pages_num', $pages_num);
		$this->design->assign('total_orders_count', $clients_count);

		$filter['page'] = $current_page;
		$filter['limit'] = $items_per_page;
        
        $mailing_ids = array();
        if ($mailings = $this->mailings->get_mailings($filter))
        {
            foreach ($mailings as $mailing)
            {
                $mailing->collectors = unserialize($mailing->collectors);
                $mailing->items = $this->mailings->get_items(array('mailing_id'=>$mailing->id));
                $mailing->sent = array_filter($mailing->items, function($var){
                    return $var->status != 1;
                });
                $mailing->sent_success = array_filter($mailing->items, function($var){
                    return $var->status == 2;
                });
                $mailing->success_mail = count($mailing->sent_success);
                $mailing->sent_mail = count($mailing->items);
                $mailing->total_mail = count($mailing->sent);
            }
        }
        
        $this->design->assign('mailings', $mailings);
        
        return $this->design->fetch('mailing_list.tpl');
    }
    
    private function action_new()
    {

        $collectors = array();
        foreach ($this->managers->get_managers() as $m)
        {
            if ($m->role == 'collector' && empty($m->blocked))
            {
                if (!isset($collectors[$m->collection_status_id]))
                    $collectors[$m->collection_status_id] = array();

                if ($this->manager->role == 'team_collector')
                {
                    if (in_array($m->id, (array)$this->manager->team_id))
                        $collectors[$m->collection_status_id][] = $m;
                }
                else
                {
                    $collectors[$m->collection_status_id][] = $m;
                }
            }
        }
        $collectors = array_filter($collectors);
        
        $this->design->assign('collectors', $collectors);

        $collection_statuses = $this->contracts->get_collection_statuses();
        $this->design->assign('collection_statuses', $collection_statuses);
        
    	return $this->design->fetch('mailing_new.tpl');
        
    }
    
    private function action_calc()
    {
        $mkk_numbers = array();
        $yuk_numbers = array();
        $collectors = $this->request->get('collectors');
        if (!empty($collectors))
        {
            if ($contracts = $this->contracts->get_contracts(array('collection_manager_id' => $collectors, 'status' => array(4,7))))
            {
                foreach ($contracts as $c)
                {
                    if (empty($c->sold))
                        $mkk_numbers[$c->user_id] = '';
                    else
                        $yuk_numbers[$c->user_id] = '';
                }
            
                if ($users = $this->users->get_users(array('id' => array_filter(array_merge(array_keys($mkk_numbers), array_keys($yuk_numbers))))))
                {
                    foreach ($users as $u)
                    {
                        if (isset($mkk_numbers[$u->id]))
                            $mkk_numbers[$u->id] = $u->phone_mobile;
                        if (isset($yuk_numbers[$u->id]))
                            $yuk_numbers[$u->id] = $u->phone_mobile;
                    }
                }
                
            }
        }
        
        header('Content-type: application/json');
        echo json_encode(array(
            'mkk' => count($mkk_numbers),
            'yuk' => count($yuk_numbers),
        ));
        exit;
    }
    
    
    
    private function sms_send($numbers, $text, $yuk = 0)
    {
        foreach ($numbers as $number)
        {
//echo 'SEND '.$number.'<br />';
            $this->sms->send($number, $text, $yuk);
        }        
    }
    
    private function zvonobot_call($numbers, $text, $yuk = 0)
    {
        $name = 'mailing_'.date('ymd');
    	$record_id = $this->zvonobot->create_record($name, $text, $yuk);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($record_id['data']['id'], $record_id);echo '</pre><hr />';        
        foreach ($numbers as $number)
        {
//echo 'CALL '.$number.'-'.$record_id['data']['id'].'<br />';
            $resp = $this->zvonobot->call($number, $record_id['data']['id'], $yuk);

            $this->zvonobot->add_zvonobot(array(
                'user_id' => 0,
                'contract_id' => 0,
                'yuk' => $yuk,
                'zvonobot_id' => isset($resp['data'][0]['id']) ? $resp['data'][0]['id'] : null,
                'status' => isset($resp['data'][0]['status']) ? $resp['data'][0]['status'] : 'new',
                'body' => serialize($resp),
                'create_date' => date('Y-m-d H:i:s'),
                'phone' => $number,
            ));
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';        
        }
    }
    
}