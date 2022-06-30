<?php

class CompanyController extends Controller
{
    public function fetch()
    {
        switch ($this->request->post('action', 'string')) :
            case 'add_branch':
                $this->action_add_branch();
                break;

            case 'edit_branch':
                $this->action_edit_branch();
                break;

            case 'get_branch':
                $this->action_get_branch();
                break;

            case 'edit_company':
                $this->action_edit_company();
                break;

            case 'change_blocked_flag':
                $this->action_change_blocked_flag_company();
                break;

            case 'delete_branche':
                $this->action_delete_branche();
                break;

            case 'delete_company':
                $this->action_delete_company();
                break;

            case 'add_settlement':
                $this->action_add_settlement();
                break;

            case 'change_std_flag':
                $this->action_change_std_flag();
                break;

            case 'get_settlement':
                $this->action_get_settlement();
                break;

            case 'update_settlement':
                $this->action_update_settlement();
                break;

            case 'delete_settlement':
                $this->action_delete_settlement();
                break;

            case 'import_payments_list':
                $this->action_import_payments_list();
                break;

            case 'import_workers_list_attestations':
                $this->action_import_workers_list_attestations();
                break;

            case 'add_document':
                $this->action_add_document();
                break;
        endswitch;

        $company_id = $this->request->get('id');

        if ($company_id == 2) {
            $settlements = $this->OrganisationSettlements->get_settlements();
            $this->design->assign('settlements', $settlements);
        }

        $company = $this->Companies->get_company_group($company_id);
        $docs = $this->Docs->get_docs($company_id);
        $this->design->assign('docs', $docs);

        $branches = $this->Branches->get_company_branches($company_id);
        $company_checks = $this->CompanyChecks->get_five_company_checks($company_id);

        $this->design->assign('company', $company);
        $this->design->assign('branches', $branches);
        $this->design->assign('company_checks', $company_checks);

        return $this->design->fetch('company.tpl');
    }

    public function action_add_branch()
    {
        $group_id = $this->request->post('group_id');
        $company_id = $this->request->post('company_id');
        $name = $this->request->post('name');
        $payday = $this->request->post('payday');
        $fio = $this->request->post('fio');
        $phone = $this->request->post('phone');

        $last_number = $this->Branches->last_number($company_id);

        if ($last_number && $last_number < 9) {
            $last_number += 1;
            $last_number = '0' . $last_number;
        } else {
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

        foreach ($branches as $branch) {
            if ($branch->number == '00') {
                $this->Branches->update_branch(['payday' => $payday], $branch->id);
            }
        }
    }

    private function action_change_blocked_flag_company()
    {
        $company_id = $this->request->post('company_id', 'integer');
        $blocked_flag = $this->request->post('value', 'integer');
        $this->Companies->update_company($company_id, ['blocked' => $blocked_flag]);
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

    private function action_edit_branch()
    {
        $branch_id = $this->request->post('branch_id');
        $name = $this->request->post('name');
        $payday = $this->request->post('payday');
        $fio = $this->request->post('fio');
        $phone = $this->request->post('phone');

        $branch =
            [
                'name' => $name,
                'payday' => $payday,
                'fio' => $fio,
                'phone' => $phone
            ];

        $this->Branches->edit_branch($branch, $branch_id);
    }

    private function action_get_branch()
    {
        $branch_id = $this->request->post('branch_id');

        $branch = $this->Branches->get_branch($branch_id);

        echo json_encode($branch);
        exit;
    }

    private function action_add_settlement()
    {
        $name = $this->request->post('name');
        $payment = $this->request->post('payment');
        $cors = $this->request->post('cors');
        $bik = $this->request->post('bik');

        $settlement =
            [
                'name' => $name,
                'payment' => $payment,
                'cors' => $cors,
                'bik' => $bik
            ];

        $this->OrganisationSettlements->add_settlements($settlement);
    }

    private function action_change_std_flag()
    {
        $settlement_id = $this->request->post('settlement_id', 'integer');

        $this->OrganisationSettlements->change_std_flag($settlement_id);
    }

    private function action_get_settlement()
    {
        $settlement_id = $this->request->post('settlement_id', 'integer');

        $settlement = $this->OrganisationSettlements->get_settlement($settlement_id);

        echo json_encode($settlement);
        exit;
    }

    private function action_update_settlement()
    {
        $id = $this->request->post('settlement_id', 'integer');
        $name = $this->request->post('name');
        $payment = $this->request->post('payment');
        $cors = $this->request->post('cors');
        $bik = $this->request->post('bik');

        $settlement =
            [
                'name' => $name,
                'payment' => $payment,
                'cors' => $cors,
                'bik' => $bik
            ];

        $this->OrganisationSettlements->update_settlements($id, $settlement);
    }

    private function action_delete_settlement()
    {
        $id = $this->request->post('settlement_id', 'integer');

        $this->OrganisationSettlements->delete_settlements($id);
    }

    private function action_import_payments_list()
    {
        $company_id = $this->request->post('company_id');

        $file = $this->request->files('file');

        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file['tmp_name']);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($file['tmp_name']);

        $fio_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('fio'));
        $income_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('income'));
        $avanse_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('avanse'));
        $payed_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('payed'));
        $middle_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('middle'));
        $ndfl_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('ndfl'));

        $indexes = [
            'fio' => $fio_column,
            'income' => $income_column,
            'avanse' => $avanse_column,
            'payed' => $payed_column,
            'middle' => $middle_column,
            'ndfl' => $ndfl_column
        ];

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 1;
        $last_row = $active_sheet->getHighestRow();
        $clients = [];

        for ($row = $first_row; $row <= $last_row; ++$row) {
            foreach ($indexes as $key => $index){
                $value = $active_sheet->getCellByColumnAndRow($index, $row)->getValue();

                $clients[$row][$key] = $value;
                $clients[$row]['company_id'] = $company_id;

            }
        }

        foreach ($clients as $client){
            $this->PaymentsAttestation->add($client);
        }

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_import_workers_list_attestations()
    {
        $company_id = $this->request->post('company_id');

        $file = $this->request->files('file');

        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file['tmp_name']);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($file['tmp_name']);

        $fio_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('fio'));
        $created_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('created'));
        $creator_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('creator'));
        $category_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('category'));
        $birth_date_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($this->request->post('birth_date'));

        $indexes = [
            'fio' => $fio_column,
            'created' => $created_column,
            'creator' => $creator_column,
            'category' => $category_column,
            'birth_date' => $birth_date_column
        ];

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 1;
        $last_row = $active_sheet->getHighestRow();
        $clients = [];

        for ($row = $first_row; $row <= $last_row; ++$row) {
            foreach ($indexes as $key => $index){
                $value = $active_sheet->getCellByColumnAndRow($index, $row)->getValue();

                if(in_array($key, ['created', 'birth_date']))
                    $value = date('Y-m-d', strtotime($value));

                if($key == 'fio')
                    $value = mb_convert_encoding($value, 'UTF-8');

                $clients[$row][$key] = $value;
                $clients[$row]['company_id'] = $company_id;

                if($key == 'creator' && empty($value)){
                    unset($clients[$row]);
                    break;
                }

            }
        }

        foreach ($clients as $client){
            $this->CompanyChecks->add($client);
        }

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_add_document()
    {
        $company_id = $this->request->post('company_id');
        $date_doc = date('Y-m-d', strtotime($this->request->post('date_doc')));
        $name = $this->request->post('name');
        $comment = $this->request->post('comment');
        $doc = $_FILES['doc'];

        $new_filename = md5(microtime() . rand()) . '.' . $doc['name'];

        move_uploaded_file($doc['tmp_name'], $this->config->root_dir . $this->config->user_files_dir . $new_filename);

        $document =
            [
                'company_id' => (int)$company_id,
                'created' => $date_doc,
                'name' => $name,
                'description' => $comment,
                'filename' => $new_filename
            ];

        $this->Docs->add_doc($document);
    }
}
