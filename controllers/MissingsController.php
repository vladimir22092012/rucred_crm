<?php

ini_set('max_execution_time', 40);

class MissingsController extends Controller
{

    public function fetch()
    {
        $status = $this->request->get('status');
        $page = $this->request->get('page');

        switch ($status) {

            case 2:
                $this->getUnreable($page);
                break;

            case 3:
                $this->getUsersToUnder($page);
                break;

            default:
                $this->getReable($page);
                break;
        }

        return $this->design->fetch('missings.tpl');
    }

    private function getUnreable($page = 1)
    {
        $items_per_page = 20;

        $limit = ($page - 1) * $items_per_page;

        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $clients_count = OrdersORM::with('user')
            ->where('status', 12)
            ->where('unreability', 0)
            ->get()->count();

        $clients = OrdersORM::with('user')
            ->where('status', 12)
            ->where('unreability', 1)
            ->offset($limit)
            ->limit($items_per_page)
            ->get();

        $this->design->assign('filter_status', 2);

        $pages_num = ceil($clients_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);

        $this->design->assign('clients', $clients);
    }

    private function getReable($page = 1)
    {
        $items_per_page = 20;

        $limit = ($page - 1) * $items_per_page;

        $stageFilter = $this->request->get('stage', 'integer');
        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $clients_count = OrdersORM::with('user')
            ->where('status', 12)
            ->where('unreability', 0)
            ->get()->count();

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;
        $filter['stage_filter'] = $stageFilter;

        $clients = OrdersORM::with('user')
            ->where('status', 12)
            ->where('unreability', 0)
            ->offset($limit)
            ->limit($items_per_page)
            ->get();

        $this->design->assign('filter_status', 1);

        $pages_num = ceil($clients_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);

        $this->design->assign('clients', $clients);
    }

    private function getUsersToUnder($page = 1)
    {
        $items_per_page = 20;

        $limit = ($page - 1) * $items_per_page;

        $stageFilter = $this->request->get('stage', 'integer');
        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $clients_count = OrdersORM::with('user')
            ->where('status', '>=', 0)
            ->where('unreability', 0)
            ->get()->count();

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;
        $filter['stage_filter'] = $stageFilter;

        $clients = OrdersORM::with('user')
            ->where('status', '>=', 0)
            ->where('unreability', 0)
            ->offset($limit)
            ->limit($items_per_page)
            ->get();

        $this->design->assign('filter_status', 3);

        $pages_num = ceil($clients_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);

        $this->design->assign('clients', $clients);
    }
}
