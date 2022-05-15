<?php

require_once 'View.php';

class MissingsView extends View
{

    public function fetch()
    {
        if (!in_array('missings', $this->manager->permissions)) {
            return $this->design->fetch('403.tpl');
        }
        
        if ($this->request->method('post')) {
            switch ($this->request->post('action', 'string')) :
                case 'set_manager':
                    $this->set_manager_action();
                    break;
                
                case 'close_missing':
                    $this->close_missing_action();
                    break;
            endswitch;
        }
        
        $items_per_page = 20;

        $filter = array();

        $filter['missing'] = 300;
        
        if (in_array($this->manager->role, array('contact_center'))) {
            $filter['missing_status'] = 0;
//            $filter['missing_manager_id'] = $this->manager->id;
        }
        
        if (!($sort = $this->request->get('sort', 'string'))) {
            $sort = 'id_desc';
        }
        $filter['sort'] = $sort;
        $this->design->assign('sort', $sort);

        if ($search = $this->request->get('search')) {
            $filter['search'] = array_filter($search);
            $this->design->assign('search', array_filter($search));
        }

        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $clients_count = $this->users->count_users($filter);

        $pages_num = ceil($clients_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;

        $clients = $this->users->get_users($filter);

        foreach ($clients as $client) {
            $usersId[] = $client->id;
        }
        
        $calls = $this->mango->get_calls(array('user_id'=>$usersId));
        foreach ($clients as $client) {
            foreach ($calls as $call) {
                if ($client->id == $call->id) {
                    $client->dump = $call;
                    $client->dump->callDate = date('d-m-Y H:i:s', $call->create_time);
                }
            }
        }
        

        $clients = array_map(function ($var) {
            if (!empty($var->additional_data_added)) {
                $var->stages = 7;
                $var->last_stage_date = $var->additional_data_added_date;
            } elseif (!empty($var->files_added)) {
                $var->stages = 6;
                $var->last_stage_date = $var->files_added_date;
            } elseif (!empty($var->card_added)) {
                $var->stages = 5;
                $var->last_stage_date = $var->card_added_date;
            } elseif (!empty($var->accept_data_added)) {
                $var->stages = 4;
                $var->last_stage_date = $var->accept_data_added_date;
            } elseif (!empty($var->address_data_added)) {
                $var->stages = 3;
                $var->last_stage_date = $var->address_data_added_date;
            } elseif (!empty($var->personal_data_added)) {
                $var->stages = 2;
                $var->last_stage_date = $var->personal_data_added_date;
            } else {
                $var->stages = 1;
                $var->last_stage_date = $var->created;
            }

            return $var;
        }, $clients);

        $this->design->assign('clients', $clients);

        $sms_templates = $this->sms->get_templates(array('type' => 'missing'));
        $this->design->assign('sms_templates', $sms_templates);
                
        $statistic = new StdClass();
        
        $st_params = array(
            'date_from' => date('Y-m-d 00:00:00'),
            'date_to' => date('Y-m-d 20:00:00'),
            'missing_status' => 1,
        );
        $statistic->closed = $this->users->count_users($st_params);
        
        $cmplt_params = array(
            'date_from' => date('Y-m-d 00:00:00'),
            'date_to' => date('Y-m-d 23:59:59'),
            'missing_status' => 1,
            'completed' => 1
        );
        $statistic->completed = $this->users->count_users($cmplt_params);
        
        
        
        $this->design->assign('statistic', $statistic);

        return $this->design->fetch('missings.tpl');
    }
    
    public function set_manager_action()
    {
        if ($user_id = $this->request->post('user_id', 'integer')) {
            if ($user = $this->users->get_user($user_id)) {
                if (empty($user->missing_manager_id)) {
                    $this->users->update_user($user_id, array(
                        'missing_manager_id' => $this->manager->id
                    ));
                    
                    $this->json_output(array('success' => 1, 'manager_name' => $this->manager->name));
                } else {
                    $this->json_output(array('error' => 'Заявка уже принята'));
                }
            } else {
                $this->json_output(array('error' => 'UNDEFINED_USER'));
            }
        } else {
            $this->json_output(array('error' => 'EMPTY_USER_ID'));
        }
    }
    
    public function close_missing_action()
    {
        if ($user_id = $this->request->post('user_id', 'integer')) {
            if ($user = $this->users->get_user($user_id)) {
                if (empty($user->missing_status)) {
                    $this->users->update_user($user_id, array(
                        'missing_status' => 1
                    ));
                    
                    $this->json_output(array('success' => 1));
                } else {
                    $this->json_output(array('error' => 'Заявка уже завершена'));
                }
            } else {
                $this->json_output(array('error' => 'UNDEFINED_USER'));
            }
        } else {
            $this->json_output(array('error' => 'EMPTY_USER_ID'));
        }
    }
}
