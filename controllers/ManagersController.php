<?php

class ManagersController extends Controller
{
    public function fetch()
    {
        $items_per_page = 10;

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

        if (in_array($this->manager->role, ['underwriter', 'employer'])) {
            header('Location: /offline_orders');
        }

        if (!in_array('managers', $this->manager->permissions)) {
            return $this->design->fetch('403.tpl');
        }

        $managers = $this->managers->get_managers($filter);

        if ($this->manager->role == 'team_collector') {
            $team_id = (array)$this->manager->team_id;
            $managers = array_filter($managers, function ($var) use ($team_id) {
                return in_array($var->id, $team_id);
            });
        }

        if ($this->manager->role == 'city_manager') {
            $managers = array_filter($managers, function ($var) {
                return $var->role == 'cs_pc';
            });
        }

        $companies = $this->Companies->get_companies();

        foreach ($companies as $company) {
            foreach ($managers as $key => $manager) {
                if ($company->id == $manager->company_id) {
                    $manager->company_name = $company->name;
                }
            }
        }

        $current_page = $this->request->get('page', 'integer');
        $current_page = max(1, $current_page);
        $this->design->assign('current_page_num', $current_page);

        $clients_count = $this->users->count_users($filter);

        $pages_num = ceil($clients_count/$items_per_page);
        $this->design->assign('total_pages_num', $pages_num);
        $this->design->assign('total_orders_count', $clients_count);

        $filter['page'] = $current_page;
        $filter['limit'] = $items_per_page;

        $this->design->assign('managers', $managers);

        $roles = $this->managers->get_roles();
        $this->design->assign('roles', $roles);

        return $this->design->fetch('managers.tpl');
    }
}
