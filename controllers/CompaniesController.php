<?php

class CompaniesController extends Controller
{
    public function fetch()
    {
        switch ($this->request->post('action', 'string')) :
            case 'add_company':
                $this->action_add_company();
                break;
        endswitch;

        $filter = array();

        if ($this->request->get('sort', 'string')) {
            $filter['sort'] = $this->request->get('sort', 'string');
            $this->design->assign('sort', $filter['sort']);
        }

        if ($this->manager->role == 'employer') {
            $managers_company = $this->ManagersEmployers->get_records($this->manager->id);
            foreach ($managers_company as $id => $name) {
                $filter['employer'][] = $id;
            }
        }

        $companies = $this->Companies->get_companies_groups($filter);
        $groups = $this->Groups->get_groups($filter);

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
        $payday = $this->request->post('payday', 'integer');

        $last_number = $this->Companies->last_number($group_id);

        if ($last_number && $last_number < 10) {
            $last_number += 1;
            $last_number = '0' . $last_number;
        }

        if ($last_number == false) {
            $last_number = '01';
        }
        if ($last_number && $last_number > 10) {
            $last_number += 1;
        }

        $company =
            [
                'group_id' => $group_id,
                'number' => $last_number,
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

        $this->action_add_brunch($group_id, $company_id, 'По умолчанию', $payday);
        exit;
    }

    private function action_add_brunch($group_id, $company_id, $branches_name, $branches_payday)
    {
        $branch =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'number' => '00',
                'name' => $branches_name,
                'payday' => $branches_payday
            ];

        $this->Branches->add_branch($branch);
    }
}
