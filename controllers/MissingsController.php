<?php

ini_set('max_execution_time', 40);

class MissingsController extends Controller
{

    private $filter = [];

    public function fetch()
    {
        $status = $this->request->get('status');
        $page = $this->request->get('page');
        $sort = $this->request->get('sort');

        if (!$period = $this->request->get('period')) {
            $period = 'all';
        }


        switch ($period) :
            case 'today':
                $this->filter['date_from'] = date('Y-m-d 00:00:01');
                $this->filter['date_to'] = date('Y-m-d 23:59:59');
                break;

            case 'yesterday':
                $this->filter['date_from'] = date('Y-m-d 00:00:01', time() - 86400);
                $this->filter['date_to'] = date('Y-m-d 23:59:59', time() - 86400);
                break;

            case 'month':
                $this->filter['date_from'] = date('Y-m-01 00:00:01');
                $this->filter['date_to'] = date('Y-m-t 23:59:59');
                break;

            case 'year':
                $this->filter['date_from'] = date('Y-01-01 00:00:01');
                $this->filter['date_to'] = date('Y-12-31 23:59:59');
                break;

            case 'all':
                $this->filter['date_from'] = null;
                $this->filter['date_to'] = null;
                break;

            case 'optional':
                $daterange = $this->request->get('daterange');
                if ($daterange) {
                    $filter_daterange = array_map('trim', explode('-', $daterange));
                    $this->filter['date_from'] = date('Y-m-d 00:00:01', strtotime($filter_daterange[0]));
                    $this->filter['date_to'] = date('Y-m-d 23:59:59', strtotime($filter_daterange[1]));
                } else {
                    $this->filter['date_from'] = date('Y-m-d 00:00:01');
                    $this->filter['date_to'] = date('Y-m-d 23:59:59');
                }
                break;
        endswitch;

        $this->design->assign('period', $period);
        $this->design->assign('from', $this->filter['date_from']);
        $this->design->assign('top', $this->filter['date_to'] );

        if(empty($sort))
            $sort = 'modified desc';

        $clients_all = OrdersORM::with('user')
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
            ->get()->count();
        $this->design->assign('clients_all', $clients_all);

        $clients_unreable = OrdersORM::with('user')
            ->where('status', 12)
            ->where('unreability', 0)
            ->where('first_loan', 1)
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
            ->get()->count();
        $this->design->assign('clients_unreable', $clients_unreable);

        $clients_to_under = OrdersORM::with('user')
            ->where('status', '>=', 0)
            ->where('s_orders.status', '!=', 12)
            ->where('unreability', 0)
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
            ->get()->count();
        $this->design->assign('clients_to_under', $clients_to_under);

        $clients_reable = OrdersORM::with('user')
            ->where('status', 12)
            ->where('unreability', 0)
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
            ->get()->count();
        $this->design->assign('clients_reable', $clients_reable);

        switch ($status) {

            case 'all':
                $this->getAll($sort, $page);
                break;

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
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
            ->get()->count();

        if(in_array($sorting[0], ['lastname', 'phone_mobile']))
            $modifier = 's_users.';
        else
            $modifier = 's_orders.';

        $clients = OrdersORM::select('s_orders.*')
            ->join('s_users', 's_orders.user_id', '=', 's_users.id')
            ->where('s_orders.status', 12)
            ->where('s_orders.unreability', 1)
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('s_orders.begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
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
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
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
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('s_orders.begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
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
            ->where('s_orders.status', '!=', 12)
            ->where('unreability', 0)
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
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
            ->where('s_orders.status', '!=', 12)
            ->where('s_orders.unreability', 0)
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('s_orders.begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
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

    private function getAll($sort, $page = 1) {
        $items_per_page = 20;

        $limit = ($page - 1) * $items_per_page;

        $sorting = explode(' ', $sort);

        $stageFilter = $this->request->get('stage', 'integer');
        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $clients_count = OrdersORM::with('user')
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
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
            ->where(function($query) {
                if ($this->filter['date_from'] && $this->filter['date_to']) {
                    $query->whereBetween('s_orders.begin_registration', [$this->filter['date_from'], $this->filter['date_to']]);
                }
            })
            ->orderBy($modifier.$sorting[0], $sorting[1])
            ->offset($limit)
            ->limit($items_per_page)
            ->get();

        $this->design->assign('filter_status', 'all');

        $pages_num = ceil($clients_count / $items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);
        $this->design->assign('sort', $sort);

        $this->design->assign('clients', $clients);
    }
}
