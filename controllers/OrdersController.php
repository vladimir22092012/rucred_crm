<?php

class OrdersController extends Controller
{
    public function fetch()
    {
        $items_per_page = 100;

        $filter = array();

        $filter['offline'] = 1;
        $this->design->assign('offline', $filter['offline']);

        if (!($period = $this->request->get('period'))) {
            $period = 'all';
        }

        switch ($period) :
            case 'today':
                $filter['date_from'] = date('Y-m-d');
                break;

            case 'yesterday':
                $filter['date_from'] = date('Y-m-d', time() - 86400);
                $filter['date_to'] = date('Y-m-d', time() - 86400);
                break;

            case 'month':
                $filter['date_from'] = date('Y-m-01');
                break;

            case 'year':
                $filter['date_from'] = date('Y-01-01');
                break;

            case 'all':
                $filter['date_from'] = null;
                $filter['date_to'] = null;
                break;

            case 'optional':
                $daterange = $this->request->get('daterange');
                $filter_daterange = array_map('trim', explode('-', $daterange));
                $filter['date_from'] = date('Y-m-d', strtotime($filter_daterange[0]));
                $filter['date_to'] = date('Y-m-d', strtotime($filter_daterange[1]));
                break;
        endswitch;
        $this->design->assign('period', $period);

        /*
                // показывать менеджеру только его заявки
                if ($this->manager->role == 'user')
                {
                    $filter['current'] = $this->manager->id;
                }
        */
        if ($this->manager->role == 'collector' || $this->manager->role == 'chief_collector') {
            // показываем только выданные заявки
            $filter['status'] = array(5);
        }

        if ($this->manager->role == 'quality_control') {
            $filter['workout_sort'] = 1;
        }

        $filter['offline'] = 0;

        if (!in_array($this->manager->role, array('collector', 'chief_collector', 'developer'))) {
            // показываем заявки только созданные на сайте
            $filter['type'] = 'base';
        }

        if (!($sort = $this->request->get('sort', 'string'))) {
            $sort = 'order_id_desc';
        }
        $filter['sort'] = $sort;
        $this->design->assign('sort', $sort);

        if ($search = $this->request->get('search')) {
            $filter['search'] = array_filter($search);
            $this->design->assign('search', array_filter($search));
        }

        if ($filter_client = $this->request->get('client')) {
            $filter['client'] = $filter_client;
            $this->design->assign('filter_client', $filter_client);
        }

        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $orders_count = $this->orders->count_orders($filter);

        $pages_num = ceil($orders_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $orders_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;

        if ($this->manager->role == 'employer') {
            $managers_company = $this->ManagersEmployers->get_records($this->manager->id);
            foreach ($managers_company as $id => $name) {
                $filter['employer'][] = $id;
            }
        }

        $status = $this->request->get('status');

        if(!empty($status)){
            $filter['status'] = $status;
            $this->design->assign('filter_status', $status);
        }else{
            $filter['status'] = [0,1,2,4,6,8,9,14,15,10,11,13];
        }

        $orders_source = $this->request->get('source');

        if(!empty($orders_source)){
            $filter['order_source'] = $orders_source;
            $this->design->assign('filter_source', $orders_source);
        }

        $orders = array();
        foreach ($this->orders->get_orders($filter) as $order) {
            $order->number = str_pad($order->order_id, 5, '0', STR_PAD_LEFT);
            $order->scorings = array();
            $order->penalties = array();
            foreach ($this->scorings->get_scorings(array('user_id' => $order->user_id)) as $sc) {
                $order->scorings[$sc->type] = $sc;
            }
            if (empty($order->scorings) || !count($order->scorings)) {
                $order->scorings_result = 'Не проводился';
            } else {
                $order->scorings_result = 'Пройден';
                foreach ($order->scorings as $scoring) {
                    if (!$scoring->success) {
                        $order->scorings_result = 'Не пройден: ' . $scoring->type;
                    }
                }
            }

            $order->count_schedules = count($this->PaymentsSchedules->gets($order->order_id));

            if (!empty($order->contract_id)) {
                $order->contract = $this->contracts->get_contract((int)$order->contract_id);
            }

            $orders[$order->order_id] = $order;
        }

        if ($penalties = $this->penalties->get_penalties(array('order_id' => array_keys($orders)))) {
            foreach ($penalties as $p) {
                if (isset($orders[$p->order_id])) {
                    $orders[$p->order_id]->penalties[] = $p;
                }
            }
        }

        foreach ($orders as $order) {
            $user_close_orders = $this->orders->get_orders(array(
                'user_id' => $order->user_id,
                'type' => 'base',
                'status' => array(7)
            ));
            $order->have_crm_closed = !empty($user_close_orders);
        }

        $managers = array();
        foreach ($this->managers->get_managers() as $m) {
            $managers[$m->id] = $m;
        }
        $this->design->assign('managers', $managers);

        $scoring_types = $this->scorings->get_types();
        $this->design->assign('scoring_types', $scoring_types);

        $sms_templates = $this->sms->get_templates(array('type' => 'order'));
        $this->design->assign('sms_templates', $sms_templates);

        $companies = $this->Companies->get_companies();
        $groups = $this->Groups->get_groups();

        foreach ($orders as $order) {
            foreach ($companies as $company) {
                if ($order->company_id == $company->id) {
                    $order->company_name = $company->name;
                    $order->company_number = $company->number;
                }
            }
        }

        foreach ($orders as $order) {
            foreach ($groups as $group) {
                if ($order->group_id == $group->id)
                    $order->group_number = $group->number;
            }

            $old_orders = $this->orders->get_orders(['user_id' => $order->user_id]);

            $order->client_status = 'Повтор';

            if(count($old_orders) > 1){
                foreach ($old_orders as $old_order){
                    if(in_array($old_order->status, [5,7]))
                        $order->client_status = 'ПК';
                }
            }

            if(count($old_orders) == 1)
                $order->client_status = 'Новая';
        }


        $this->design->assign('orders', $orders);

        return $this->design->fetch('orders.tpl');
    }
}
