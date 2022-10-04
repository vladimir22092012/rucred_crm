<?php

ini_set('max_execution_time', 40);

class MissingsController extends Controller
{

    public function fetch()
    {
        if ($this->request->method('post')) {
            switch ($this->request->post('action', 'string')) :
                case 'set_manager':
                    $this->set_manager_action();
                    break;

                case 'close_missing':
                    $this->close_missing_action();
                    break;

                case 'send_sms':
                    $this->send_sms_action();
                    break;
            endswitch;
        }

        $items_per_page = 20;

        $filter = array();

        if (!($sort = $this->request->get('sort', 'string'))) {
            $sort = 'id_desc';
        }
        $filter['sort'] = $sort;
        $this->design->assign('sort', $sort);

        if ($search = $this->request->get('search')) {
            $filter['search'] = array_filter($search);
            $this->design->assign('search', array_filter($search));
        }

        $filter['stage_filter'] = 1;

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

        foreach ($clients as $client)
        {
            $client->order = $this->orders->get_by_user($client->id);
        }

        $this->design->assign('clients', $clients);


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
            'text' => 'Клиенту отправлено смс с текстом: '.$template->template,
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
        $this->json_output(array('success'=>true));
    }
}
