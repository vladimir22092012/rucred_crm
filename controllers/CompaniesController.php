<?php

class CompaniesController extends Controller
{
    public function fetch()
    {
        switch ($this->request->post('action', 'string')):
            case 'add_company':
                $this->action_add_company();
                break;

        endswitch;

        $companies = $this->Companies->get_companies_groups();
        $groups = $this->Groups->get_groups();

        $this->design->assign('companies', $companies);
        $this->design->assign('groups', $groups);

        return $this->design->fetch('companies.tpl');
    }

    private function action_add_company()
    {
        $group_id = $this->request->post('group');
        $name = $this->request->post('name');
        $eio_position = $this->request->post('eio_position');
        $eio_fio = $this->request->post('eio_fio');
        $inn = $this->request->post('inn');
        $ogrn = $this->request->post('ogrn');
        $kpp = $this->request->post('kpp');
        $jur_address = $this->request->post('jur_address');
        $phys_address = $this->request->post('phys_address');

        $last_id = $this->Companies->last_id();
        $number = $last_id + 1;

        if ($last_id < 10)
            $number = '0' . $number;

        $company =
            [
                'group_id' => $group_id,
                'number' => $number,
                'name' => $name,
                'eio_position' => $eio_position,
                'eio_fio' => $eio_fio,
                'inn' => $inn,
                'ogrn' => $ogrn,
                'kpp' => $kpp,
                'jur_address' => $jur_address,
                'phys_address' => $phys_address
            ];

        $company_id = $this->Companies->add_company($company);

        $this->action_add_brunch($group_id, $company_id, 'По умолчанию', 10);
        exit;
    }

    private function action_add_brunch($group_id, $company_id, $branches_name, $branches_payday)
    {
        $last_id = $this->Branches->last_id();
        $number = $last_id + 1;

        if ($last_id < 10)
            $number = '0' . $number;

        if(empty($last_id))
            $number = '00';

        $branch =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'number' => $number,
                'name' => $branches_name,
                'payday' => $branches_payday
            ];

        $this->Branches->add_branch($branch);
    }
}