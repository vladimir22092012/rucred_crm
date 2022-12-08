<?php

class LoantypeController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            if ($this->request->post('action', 'string')) {
                $methodName = 'action_' . $this->request->post('action', 'string');
                if (method_exists($this, $methodName)) {
                    $this->$methodName();
                }
            }
        }

        if ($id = $this->request->get('id', 'integer')) {

            $loantype = $this->loantypes->get_loantype($id);

            $groups = $this->GroupLoanTypes->get_loantype_groups($id);

            $this->design->assign('groups', $groups);

            if ($this->manager->role == 'employer') {
                foreach ($groups as $group) {
                    if ($this->manager->group_id == $group['id']) {
                        $loantype->percent = $group['standart_percents'];
                        $loantype->profunion = $group['preferential_percents'];
                    }
                }
            }
        }


        if (!empty($loantype)) {
            $this->design->assign('loantype', $loantype);
        }

        $manager_role = $this->manager->role;
        $this->design->assign('manager_role', $manager_role);


        return $this->design->fetch('offline/loantype.tpl');
    }

    private function action_edit_loan()
    {
        $loantype_id = $this->request->post('id', 'integer');

        $loantype = new StdClass();

        $loantype->name = $this->request->post('name');

        $loantype->percent = $this->request->post('percent');
        $loantype->type = $this->request->post('product_type');
        $loantype->percent = str_replace(',', '.', $loantype->percent);
        $loantype->profunion = $this->request->post('profunion');
        $loantype->profunion = str_replace(',', '.', $loantype->profunion);
        $loantype->min_amount = $this->request->post('min_amount');
        $loantype->number = $this->request->post('number');
        $loantype->max_amount = $this->request->post('max_amount');
        $loantype->min_amount = str_replace(' ', '', $loantype->min_amount);
        $loantype->max_amount = str_replace(' ', '', $loantype->max_amount);
        $loantype->max_period = $this->request->post('max_period', 'integer');
        $loantype->online_flag = $this->request->post('online_flag', 'integer');
        $loantype->reason_flag = $this->request->post('reason_flag');
        $loantype->description = $this->request->post('description');

        $check_uniq = $this->Loantypes->check_uniq_number($loantype->number, $loantype_id);

        if (!empty($check_uniq))
            $this->design->assign('error', 'Такой номер уже есть');

        if (empty($loantype->name)) {
            $this->design->assign('error', 'Укажите наименование вида кредита');
        } elseif (empty($loantype->percent)) {
            $this->design->assign('error', 'Выберите процентную ставку');
        } elseif (empty($loantype->max_amount)) {
            $this->design->assign('error', 'Укажите максимальную сумму кредита');
        } elseif (mb_strlen($loantype->description) > 20) {
            $this->design->assign('error', 'Длина описания не может быть более 20 символов');
        } elseif (empty($loantype->max_period)) {
            $this->design->assign('error', 'Укажите максимальный срок кредита');
        } elseif ($loantype->type === 'pdl' && $loantype->max_period > 1
            || $loantype->type === 'annouitet' && $loantype->max_period <= 1
        ) {
            $this->design->assign('error', 'Для данного типа продукта, данное количество выплат недоступно');
        } else {
            if (empty($loantype_id)) {
                $loantype->id = $this->loantypes->add_loantype($loantype);

                $groups = $this->Groups->get_groups();

                foreach ($groups as $group) {
                    $group =
                        [
                            'group_id' => $group->id,
                            'loantype_id' => $loantype->id,
                            'standart_percents' => $loantype->percent,
                            'preferential_percents' => $loantype->profunion
                        ];

                    $this->GroupLoanTypes->add_group($group);
                }

                $this->design->assign('success', 'Вид кредитования добавлен');
            } else {
                $loantype->id = $this->loantypes->update_loantype($loantype_id, $loantype);
                $this->design->assign('success', 'Вид кредитования изменен');
            }
        }
    }

    private function action_edit_tarif()
    {
        $standart_percents = $this->request->post('standart_percents');
        $preferential_percents = $this->request->post('preferential_percents');
        $loantype_id = $this->request->post('loantype_id', 'integer');
        $group_id = $this->request->post('group_id', 'integer');
        $individual = $this->request->post('individual');

        $loanType = LoantypesORM::find($loantype_id);

        if($individual > $loanType->max_amount)
        {
            echo json_encode(['error' => 'Сумма больше максимальной для тарифа']);
            exit;
        }

        if($individual < $loanType->min_amount)
        {
            echo json_encode(['error' => 'Сумма меньше минимальной для тарифа']);
            exit;
        }

        $record =
            [
                'standart_percents' => $standart_percents,
                'preferential_percents' => $preferential_percents,
                'loantype_id' => $loantype_id,
                'group_id' => $group_id,
                'individual' => $individual
            ];

        $this->GroupLoanTypes->update_record($record);

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_change_online_flag()
    {
        $loantype_id = $this->request->post('loantype_id', 'integer');
        $flag = $this->request->post('flag', 'integer');

        $loan_update = ['online_flag' => $flag];

        $this->Loantypes->update_loantype($loantype_id, $loan_update);
    }

    private function action_change_on_off_flag()
    {
        $record_id = $this->request->post('record_id', 'integer');
        $flag = $this->request->post('value', 'integer');

        $this->GroupLoanTypes->change_on_off_flag($record_id, $flag);
    }
}
