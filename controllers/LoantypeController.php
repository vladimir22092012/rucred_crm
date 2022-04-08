<?php

class LoantypeController extends Controller
{
    public function fetch()
    {
        if ($this->request->post('action') == 'edit_loan') {
            $loantype_id = $this->request->post('id', 'integer');

            $loantype = new StdClass();

            $loantype->name = $this->request->post('name');
            $loantype->organization_id = $this->request->post('organization_id', 'integer');
            $loantype->percent = $this->request->post('percent');
            $loantype->profunion = $this->request->post('profunion');
            $loantype->discount = $this->request->post('discount');
            $loantype->min_amount = $this->request->post('min_amount');
            $loantype->max_amount = $this->request->post('max_amount', 'integer');
            $loantype->max_period = $this->request->post('max_period', 'integer');

            $loantype->reason_flag = ($this->request->post('reason_flag', 'integer') == 1) ? 0 : 1;


            if (empty($loantype->name)) {
                $this->design->assign('error', 'Укажите наименование вида кредита');
            } elseif (empty($loantype->organization_id)) {
                $this->design->assign('error', 'Выберите организацию');
            } elseif (empty($loantype->percent)) {
                $this->design->assign('error', 'Выберите процентную ставку');
            } elseif (empty($loantype->max_amount)) {
                $this->design->assign('error', 'Укажите максимальную сумму кредита');
            } elseif (empty($loantype->max_period)) {
                $this->design->assign('error', 'Укажите максимальный срок кредита');
            } else {
                if (empty($loantype_id)) {
                    $loantype->id = $this->loantypes->add_loantype($loantype);
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

        if ($id = $this->request->get('id', 'integer')) {
            $loantype = $this->loantypes->get_loantype($id);

            $groups = $this->GroupLoanTypes->get_loantype_groups($id);
            $this->design->assign('groups', $groups);
        }


        if (!empty($loantype))
            $this->design->assign('loantype', $loantype);

        $organizations = array();
        foreach ($this->offline->get_organizations() as $org)
            $organizations[$org->id] = $org;
        $this->design->assign('organizations', $organizations);


        return $this->design->fetch('offline/loantype.tpl');
    }

}