<?php

error_reporting(-1);
ini_set('display_errors', 'On');
class CompanyController extends Controller
{
    public function fetch()
    {
        switch ($this->request->post('action', 'string')):
            case 'add_branch':
                $this->action_add_branch();
                break;

            case 'edit_company':
                $this->action_edit_company();
                break;

            case 'delete_branche':
                $this->action_delete_branche();
                break;

            case 'delete_company':
                $this->action_delete_company();
                break;

        endswitch;

        $company_id = $this->request->get('id');

        $company = $this->Companies->get_company_group($company_id);
        $branches = $this->Branches->get_company_branches($company_id);

        $this->design->assign('company', $company);
        $this->design->assign('branches', $branches);

        return $this->design->fetch('company.tpl');
    }

    public function action_add_branch()
    {
        $group_id = $this->request->post('group_id');
        $company_id = $this->request->post('company_id', 'integer');
        $name = $this->request->post('name');
        $payday = $this->request->post('payday');
        $fio = $this->request->post('fio');
        $phone = $this->request->post('phone');

        $last_number = $this->Branches->last_number($company_id);

        if ($last_number && $last_number < 10) {
            $last_number += 1;
            $last_number = '0' . $last_number;
        }

        if($last_number &&  $last_number > 10) {
            $last_number += 1;
        }

        $branch =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'number' => $last_number,
                'name' => $name,
                'payday' => $payday,
                'fio' => $fio,
                'phone' => $phone
            ];

        $this->Branches->add_branch($branch);
    }

    private function action_edit_company()
    {
        $company_id = $this->request->post('company_id');
        $name = $this->request->post('name');
        $eio_position = $this->request->post('eio_position');
        $eio_fio = $this->request->post('eio_fio');
        $inn = $this->request->post('inn');
        $ogrn = $this->request->post('ogrn');
        $kpp = $this->request->post('kpp');
        $jur_address = $this->request->post('jur_address');
        $phys_address = $this->request->post('phys_address');
        $payday = $this->request->post('payday');

        $company =
            [
                'name' => $name,
                'eio_position' => $eio_position,
                'eio_fio' => $eio_fio,
                'inn' => $inn,
                'ogrn' => $ogrn,
                'kpp' => $kpp,
                'jur_address' => $jur_address,
                'phys_address' => $phys_address
            ];

        $this->Companies->update_company($company_id, $company);

        $branches = $this->Branches->get_branches(['company_id' => (int)$company_id]);

        foreach($branches as $branch)
        {
            if($branch->number == '00')
                $this->Branches->update_branch(['payday' => $payday], $branch->id);
        }
    }

    private function action_delete_branche()
    {
        $branches_id = $this->request->post('branches_id', 'integer');

        $this->Branches->delete_branche($branches_id);
    }

    private function action_delete_company()
    {
        $company_id = $this->request->post('company_id', 'integer');

        $this->Companies->delete_company($company_id);
        $this->Branches->delete_branches(['company_id' => $company_id]);
    }
}