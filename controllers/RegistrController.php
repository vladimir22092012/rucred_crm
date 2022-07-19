<?php


class RegistrController extends Controller

{
    public function fetch()
    {
        $items_per_page = 20;

        $filter = array();
        $filter['status'] = [5,7];

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

        if($this->manager->role == 'employer'){
            $managers_company = $this->ManagersEmployers->get_records($this->manager->id);
            foreach ($managers_company as $id => $name){
                $filter['employer'][] = $id;
            }
        }

        $orders_count = $this->orders->count_orders($filter);

        $pages_num = ceil($orders_count/$items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $orders_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;

        $orders = $this->orders->get_orders($filter);

        foreach ($orders as $order){
            $order->number = str_pad($order->order_id, 5, '0', STR_PAD_LEFT);

            $old_orders = $this->orders->get_orders(['user_id' => $order->user_id]);

            $order->client_status = 'Повтор';

            if(count($old_orders) > 1){
                foreach ($old_orders as $old_order){
                    if(in_array($old_order->status, [5,7]))
                        $order->client_status = 'ПК';
                }
            }

            if(count($orders) == 1)
                $order->client_status = 'Новая';

            if (!empty($order->contract_id)) {
                $order->contract = $this->contracts->get_contract((int)$order->contract_id);
            }
        }

        $managers = array();
        foreach ($this->managers->get_managers() as $m) {
            $managers[$m->id] = $m;
        }
        $this->design->assign('managers', $managers);

        $scoring_types = $this->scorings->get_types();
        $this->design->assign('scoring_types', $scoring_types);

        $statuses = $this->orders->get_statuses();
        $this->design->assign('statuses', $statuses);

        $this->design->assign('orders', $orders);

        return $this->design->fetch('registr.tpl');
    }
}