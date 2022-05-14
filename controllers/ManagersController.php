<?php

class ManagersController extends Controller
{
    public function fetch()
    {

        if (in_array($this->manager->role, ['underwriter', 'employer']))
            header('Location: /offline_orders');

        if (!in_array('managers', $this->manager->permissions))
            return $this->design->fetch('403.tpl');

        $managers = $this->managers->get_managers();

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
            foreach ($managers as $key => $manager){
                if($company->id == $manager->company_id){
                    $manager->company_name = $company->name;
                }
            }
        }

        $this->design->assign('managers', $managers);

        $roles = $this->managers->get_roles();
        $this->design->assign('roles', $roles);

        return $this->design->fetch('managers.tpl');
    }

}