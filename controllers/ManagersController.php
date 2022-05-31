<?php

class ManagersController extends Controller
{
    public function fetch()
    {
        $items_per_page = 50;

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

        if (isset($search['company_name'])) {
            $companies = $this->Companies->get_companies($search);

            foreach ($companies as $company) {
                $managers_id = $this->ManagersEmployers->get_managers_id($company->id);
            }

            foreach ($managers_id as $key => $id){
                $filter['id'][] = $id->manager_id;
            }
        }

        if (!in_array('managers', $this->manager->permissions)) {
            return $this->design->fetch('403.tpl');
        }

        if ($this->manager->role == 'employer')
            $filter['employer'] = $this->manager->company_id;

        $managers = $this->managers->get_managers($filter);


        foreach ($managers as $key => $manager) {
            $managers[$key]->companies = $this->ManagersEmployers->get_records($manager->id);
        }

        if (!empty($managers)) {

//            $companies = $this->Companies->get_companies();
//
//            foreach ($companies as $company) {
//                foreach ($managers as $key => $manager) {
//                    if ($company->id == $manager->company_id) {
//                        $manager->company_name = $company->name;
//                    }
//                }
//            }

            $current_page = $this->request->get('page', 'integer');
            $current_page = max(1, $current_page);
            $this->design->assign('current_page_num', $current_page);

            $clients_count = count($managers);

            $pages_num = ceil($clients_count / $items_per_page);
            $this->design->assign('total_pages_num', $pages_num);
            $this->design->assign('total_orders_count', $clients_count);

            $filter['page'] = $current_page;
            $filter['limit'] = $items_per_page;

            $this->design->assign('managers', $managers);

            $roles = $this->managers->get_roles();
            $this->design->assign('roles', $roles);

            $manager_role = $this->manager->role;
            $this->design->assign('manager_role', $manager_role);
        }

        return $this->design->fetch('managers.tpl');
    }
}
