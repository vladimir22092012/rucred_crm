<?php

class CompanyController extends Controller
{
    public function fetch()
    {
        switch ($this->request->post('action', 'string')):
            case 'add_branch':
                $this->action_add_branch();
                break;

        endswitch;

        $company_id = $this->request->get('id');

        $company = $this->Companies->get_company_group($company_id);
        $branches = $this->Branches->get_company_branches($company_id);

        $this->design->assign('company', $company);
        $this->design->assign('branches', $branches);

        return $this->design->fetch('company.tpl');
    }

    private function action_add_branch()
    {
        $group_id = $this->request->post('group_id');
        $company_id = $this->request->post('company_id');
        $name = $this->request->post('name');
        $payday = $this->request->post('payday');

        $last_id = $this->Branches->last_id();
        $number = $last_id + 1;

        if ($last_id < 10)
            $number = '0' . $number;

        $branch =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'number' => $number,
                'name' => $name,
                'payday' => $payday
            ];

        $this->Branches->add_branch($branch);
    }
}