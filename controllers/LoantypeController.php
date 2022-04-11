<?php

class LoantypeController extends Controller
{
    public function fetch()
    {
        if ($this->request->post('action') == 'edit_loan') {
            $loantype_id = $this->request->post('id', 'integer');

            $loantype = new StdClass();

            $loantype->name = $this->request->post('name');
            $loantype->percent = $this->request->post('percent');
            $loantype->percent = str_replace(',', '.', $loantype->percent);
            $loantype->profunion = $this->request->post('profunion');
            $loantype->profunion = str_replace(',', '.', $loantype->profunion);
            $loantype->min_amount = $this->request->post('min_amount');
            $loantype->max_amount = $this->request->post('max_amount', 'integer');
            $loantype->max_period = $this->request->post('max_period', 'integer');
            $loantype->online_flag = $this->request->post('online_flag', 'integer');

            if (empty($loantype->name)) {
                $this->design->assign('error', 'Укажите наименование вида кредита');
            } elseif (empty($loantype->percent)) {
                $this->design->assign('error', 'Выберите процентную ставку');
            } elseif (empty($loantype->max_amount)) {
                $this->design->assign('error', 'Укажите максимальную сумму кредита');
            } elseif (empty($loantype->max_period)) {
                $this->design->assign('error', 'Укажите максимальный срок кредита');
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

        if ($this->request->post('action') == 'edit_tarif') {
            $standart_percents = $this->request->post('standart_percents');
            $preferential_percents = $this->request->post('preferential_percents');
            $loantype_id = $this->request->post('loantype_id', 'integer');
            $group_id = $this->request->post('group_id', 'integer');

            $record =
                [
                    'standart_percents' => $standart_percents,
                    'preferential_percents' => $preferential_percents,
                    'loantype_id' => $loantype_id,
                    'group_id' => $group_id
                ];

            $this->GroupLoanTypes->update_record($record);
        }

        if ($this->request->post('action') == 'change_online_flag') {
            $loantype_id = $this->request->post('loantype_id', 'integer');
            $flag = $this->request->post('flag', 'integer');

            $loan_update = ['online_flag' => $flag];

            $this->Loantypes->update_loantype($loantype_id, $loan_update);
        }

        if ($id = $this->request->get('id', 'integer')) {
            $loantype = $this->loantypes->get_loantype($id);

            $groups = $this->GroupLoanTypes->get_loantype_groups($id);
            $this->design->assign('groups', $groups);
        }


        if (!empty($loantype))
            $this->design->assign('loantype', $loantype);


        return $this->design->fetch('offline/loantype.tpl');
    }
}