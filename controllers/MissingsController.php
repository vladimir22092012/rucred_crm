<?php

ini_set('max_execution_time', 40);

class MissingsController extends Controller
{

    public function fetch()
    {
        $status = $this->request->get('status');
        $page = $this->request->get('page');
        $sort = $this->request->get('sort');

        if(empty($sort))
            $sort = 'modified desc';

        switch ($status) {

            case 2:
                $this->getUnreable($sort, $page);
                break;

            case 3:
                $this->getUsersToUnder($sort, $page);
                break;

            default:
                $this->getReable($sort, $page);
                break;
        }

        return $this->design->fetch('missings.tpl');
    }

    private function getUnreable($sort, $page = 1)
    {
        $items_per_page = 20;

        $limit = ($page - 1) * $items_per_page;

        $sorting = explode(' ', $sort);

        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $clients_count = OrdersORM::with('user')
            ->where('status', 12)
            ->where('unreability', 0)
            ->get()->count();

        if(in_array($sorting[0], ['lastname', 'phone_mobile']))
            $modifier = 's_users.';
        else
            $modifier = 's_orders.';

        $clients = OrdersORM::select('s_orders.*')
            ->join('s_users', 's_orders.user_id', '=', 's_users.id')
            ->where('s_orders.status', 12)
            ->where('s_orders.unreability', 1)
            ->orderBy($modifier.$sorting[0], $sorting[1])
            ->offset($limit)
            ->limit($items_per_page)
            ->get();

        $this->design->assign('filter_status', 2);

        $pages_num = ceil($clients_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);
        $this->design->assign('sort', $sort);
        $this->design->assign('clients', $clients);
    }

    private function getReable($sort, $page = 1)
    {
        $items_per_page = 20;

        $limit = ($page - 1) * $items_per_page;

        $sorting = explode(' ', $sort);

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

        if(in_array($sorting[0], ['lastname', 'phone_mobile']))
            $modifier = 's_users.';
        else
            $modifier = 's_orders.';

        $clients = OrdersORM::select('s_orders.*')
            ->join('s_users', 's_orders.user_id', '=', 's_users.id')
            ->where('s_orders.status', 12)
            ->where('s_orders.unreability', 0)
            ->orderBy($modifier.$sorting[0], $sorting[1])
            ->offset($limit)
            ->limit($items_per_page)
            ->get();

        $this->design->assign('filter_status', 1);

        $pages_num = ceil($clients_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);
        $this->design->assign('sort', $sort);
        $this->design->assign('clients', $clients);
    }

    private function getUsersToUnder($sort, $page = 1)
    {
        $items_per_page = 20;

        $limit = ($page - 1) * $items_per_page;

        $sorting = explode(' ', $sort);

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

        if(in_array($sorting[0], ['lastname', 'phone_mobile']))
            $modifier = 's_users.';
        else
            $modifier = 's_orders.';

        $clients = OrdersORM::select('s_orders.*')
            ->join('s_users', 's_orders.user_id', '=', 's_users.id')
            ->where('s_orders.status', '>=', 0)
            ->where('s_orders.unreability', 0)
            ->orderBy($modifier.$sorting[0], $sorting[1])
            ->offset($limit)
            ->limit($items_per_page)
            ->get();

        $this->design->assign('filter_status', 3);

        $pages_num = ceil($clients_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);
        $this->design->assign('sort', $sort);

        $this->design->assign('clients', $clients);
    }
}
