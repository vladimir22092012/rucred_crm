<?php

use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

            case 'impor_payments_list':
                $this->action_import_payments_list();
                break;

            case 'import_workers_list_attestations':
                $this->action_import_workers_list_attestations();
                break;

            case 'add_document':
                $this->action_add_document();
                break;

            case 'wrong_info':
                $this->action_wrong_info();
                break;

            case 'blocked':
                $this->action_blocked();
                break;

            case 'change_permission':
                $this->action_change_permission();
                break;
        endswitch;

        $companyId = (int) $this->request->get('id');

        $company = Company::with([
            'docs', 'group', 'branches', 'managers.credentials' => function (HasMany $hasMany) use ($companyId) {
                $hasMany->where('company_id', '=', $companyId);
            }
        ])->find($companyId);


        if ($companyId === 2) {
            $settlements = $this->OrganisationSettlements->get_settlements();
            $this->design->assign('settlements', $settlements);
        }

        $this->design->assign('company', new CompanyResource($company));

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
        $date_attestation = $this->request->post('date_attestation');
        $date_attestation = date('Y-m-d', strtotime($date_attestation));

        $file = $this->request->files('file');

        if (empty($file)) {
            echo json_encode(['error' => 1, 'text' => 'Файл не был загружен']);
            exit;
        }

        $fio = $this->request->post('fio');
        $income = $this->request->post('income');
        $avanse = $this->request->post('avanse');
        $payed = $this->request->post('payed');
        $middle = $this->request->post('middle');
        $saved = $this->request->post('saved');
        $ndfl = $this->request->post('ndfl');

        if (empty($fio)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка ФИО']);
            exit;
        }
        if (empty($income)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Всего начислено']);
            exit;
        }

        if (empty($avanse)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Выплата аванса']);
            exit;
        }

        if (empty($payed)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Выплата зарплаты']);
            exit;
        }

        if (empty($middle)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Выплата в межрасчетный период']);
            exit;
        }

        if (empty($saved)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Всего удержано']);
            exit;
        }

        if (empty($ndfl)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка НФДЛ']);
            exit;
        }

        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file['tmp_name']);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($file['tmp_name']);

        $fio_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($fio);
        $income_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($income);
        $avanse_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($avanse);
        $payed_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($payed);
        $middle_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($middle);
        $saved_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($saved);
        $ndfl_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($ndfl);

        $indexes = [
            'fio' => $fio_column,
            'income' => $income_column,
            'avanse' => $avanse_column,
            'payed' => $payed_column,
            'middle' => $middle_column,
            'saved' => $saved_column,
            'ndfl' => $ndfl_column
        ];

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 1;
        $last_row = $active_sheet->getHighestRow();
        $clients = [];

        for ($row = $first_row; $row <= $last_row; ++$row) {
            foreach ($indexes as $key => $index) {
                $value = $active_sheet->getCellByColumnAndRow($index, $row)->getValue();

                $clients[$row][$key] = $value;
                $clients[$row]['company_id'] = $company_id;
                $clients[$row]['date'] = $date_attestation;

            }
        }

        foreach ($clients as $client) {
            $this->PaymentsAttestation->add($client);
        }

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_import_workers_list_attestations()
    {
        $company_id = $this->request->post('company_id');
        $date_attestation = $this->request->post('date_attestation');
        $date_attestation = date('Y-m-d', strtotime($date_attestation));

        $file = $this->request->files('file');

        if (empty($file)) {
            echo json_encode(['error' => 1, 'text' => 'Файл не был загружен']);
            exit;
        }

        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file['tmp_name']);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($file['tmp_name']);

        $fio = $this->request->post('fio');
        $created = $this->request->post('created');
        $creator = $this->request->post('creator');
        $category = $this->request->post('category');
        $birth_date = $this->request->post('birth_date');

        if (empty($fio)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка ФИО']);
            exit;
        }
        if (empty($created)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Дата действия']);
            exit;
        }

        if (empty($creator)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Кем выдано']);
            exit;
        }

        if (empty($category)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Категория']);
            exit;
        }

        if (empty($birth_date)) {
            echo json_encode(['error' => 1, 'text' => 'Не заполена колонка Дата рождения']);
            exit;
        }


        $fio_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($fio);
        $created_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($created);
        $creator_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($creator);
        $category_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($category);
        $birth_date_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($birth_date);

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
            foreach ($indexes as $key => $index) {
                $value = $active_sheet->getCellByColumnAndRow($index, $row)->getValue();

                if (in_array($key, ['created', 'birth_date']))
                    $value = date('Y-m-d', strtotime($value));

                if ($key == 'fio')
                    $value = mb_convert_encoding($value, 'UTF-8');

                $clients[$row][$key] = $value;
                $clients[$row]['company_id'] = $company_id;
                $clients[$row]['created'] = $date_attestation;

                if ($key == 'creator' && empty($value)) {
                    unset($clients[$row]);
                    break;
                }

            }
        }

        foreach ($clients as $client) {
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

    private function action_wrong_info()
    {
        $company_id = $this->request->post('company_id');
        $group_id = $this->request->post('group_id');

        $company = $this->companies->get_company($company_id);

        $ticket =
            [
                'group_id' => $group_id,
                'company_id' => $company_id,
                'client_lastname' => '',
                'client_firstname' => '',
                'client_patronymic' => '',
                'creator' => $this->manager->id,
                'text' => 'Проверьте информацию по компании ' . $company->name,
                'head' => 'Недостоверность информации по компании'
            ];

        $ticket_id = $this->Tickets->add_ticket($ticket);

        $message =
            [
                'message' => 'Проверьте информацию по компании ' . $company->name,
                'ticket_id' => $ticket_id,
                'manager_id' => $this->manager->id
            ];

        $this->TicketMessages->add_message($message);

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_blocked()
    {
        $companyId = $this->request->post('company');
        $value = (bool) $this->request->post('value');

        $company = Company::find($companyId);
        $company->managers()->where('role', 'employer')->update([
            'blocked' => $value
        ]);
        $company->update([
            'blocked' => $value
        ]);

        exit;
    }

    private function action_change_permission()
    {
        $company_id = $this->request->post('com_id');
        $permission = $this->request->post('permission');

        $this->companies->update_company($company_id, ['permissions' => $permission]);
        exit;
    }
}
