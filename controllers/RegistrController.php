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

        $orders_count = $this->orders->count_orders($filter);

        $pages_num = ceil($orders_count/$items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $orders_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;

        $orders = $this->orders->get_orders($filter);

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