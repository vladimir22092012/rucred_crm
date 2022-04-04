<?php

class OfflineOrdersController extends Controller
{
    public function fetch()
    {
        $items_per_page = 20;

        $filter = array();

        $filter['offline'] = 1;
        $this->design->assign('offline', $filter['offline']);

        if (!($period = $this->request->get('period')))
            $period = 'all';

        switch ($period):
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

        if ($this->manager->role == 'cs_pc') {
            $filter['offline'] = 1;
        }

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

        if ($status = $this->request->get('status')) {
            $filter['status'] = $status;
            $this->design->assign('filter_status', $status);
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


        /*
                $orders = array();
                foreach ($this->orders->get_orders($filter) as $order)
                {
                    $order->scorings = $this->scorings->get_scorings(array('user_id'=>$order->user_id));
                    if (empty($order->scorings) || !count($order->scorings))
                    {
                        $order->scorings_result = 'Не проводился';
                    }
                    else
                    {
                        $order->scorings_result = 'Пройден';
                        foreach ($order->scorings as $scoring)
                        {
                            if (!$scoring->success)
                                $order->scorings_result = 'Не пройден: '.$scoring->type;
                        }
                    }

                    $orders[$order->order_id] = $order;
                }
        */

        $orders = array();
        foreach ($this->orders->get_orders($filter) as $order) {
            $order->scorings = array();
            $order->penalties = array();
            foreach ($this->scorings->get_scorings(array('user_id' => $order->user_id)) as $sc)
                $order->scorings[$sc->type] = $sc;
            if (empty($order->scorings) || !count($order->scorings)) {
                $order->scorings_result = 'Не проводился';
            } else {
                $order->scorings_result = 'Пройден';
                foreach ($order->scorings as $scoring) {
                    if (!$scoring->success)
                        $order->scorings_result = 'Не пройден: ' . $scoring->type;
                }
            }

            if (!empty($order->contract_id))
                $order->contract = $this->contracts->get_contract((int)$order->contract_id);

            $orders[$order->order_id] = $order;
        }

        if ($penalties = $this->penalties->get_penalties(array('order_id' => array_keys($orders)))) {
            foreach ($penalties as $p) {
                if (isset($orders[$p->order_id]))
                    $orders[$p->order_id]->penalties[] = $p;
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
        foreach ($this->managers->get_managers() as $m)
            $managers[$m->id] = $m;
        $this->design->assign('managers', $managers);

        $scoring_types = $this->scorings->get_types();
        $this->design->assign('scoring_types', $scoring_types);

        $sms_templates = $this->sms->get_templates(array('type' => 'order'));
        $this->design->assign('sms_templates', $sms_templates);


        if ($this->request->get('drafts')) {
            foreach ($orders as $key => $order) {
                if ($order->status != 12)
                    unset ($orders[$key]);
            }

            $this->design->assign('orders', $orders);
            return $this->design->fetch('offline/drafts.tpl');
        }

        else{
            foreach ($orders as $key => $order) {
                if ($order->status == 12)
                    unset ($orders[$key]);
            }

            $this->design->assign('orders', $orders);
            return $this->design->fetch('offline/orders.tpl');
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($orders);echo '</pre><hr />';
    }

}