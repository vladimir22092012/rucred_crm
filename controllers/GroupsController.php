<?php

class GroupsController extends Controller
{
    public function fetch()
    {
        switch ($this->request->post('action', 'string')):
            case 'add_group':
                $this->action_add_group();
                break;

            case 'update_group':
                $this->action_update_group();
                break;

            case 'delete_group':
                $this->action_delete_group();
                break;

        endswitch;

        $groups = $this->Groups->get_groups();

        $this->design->assign('groups', $groups);

        return $this->design->fetch('groups.tpl');
    }

    private function action_add_group()
    {
        $name = $this->request->post('name');

        $last_id = $this->Groups->last_id();

        $number = $last_id + 1;

        if ($last_id < 10)
            $number = '0' . $number;

        if (!$last_id || $last_id == 0)
            $number = '00';

        $group =
            [
                'name' => $name,
                'number' => $number
            ];

        $group_id = $this->Groups->add_group($group);

        $loantypes = $this->Loantypes->get_loantypes();

        foreach ($loantypes as $loantype) {
            $group =
                [
                    'group_id' => $group_id,
                    'loantype_id' => $loantype->id,
                    'standart_percents' => $loantype->percent,
                    'preferential_percents' => $loantype->profunion
                ];

            $this->GroupLoanTypes->add_group($group);
        }
    }

    private function action_update_group()
    {
        $group_id = $this->request->post('group_id', 'integer');
        $group_name = $this->request->post('group_name', 'string');

        $group =
            [
              'name' => $group_name
            ];

        $this->Groups->update_group($group_id, $group);
    }

    private function action_delete_group()
    {
        $group_id = $this->request->post('group_id', 'integer');

        $branches = $this->Branches->get_branches_by_group($group_id);

        if(count($branches) > 1)
        {
            echo 'Ошибка. Количество филиалов более 1';
            exit;
        }

        else{
            $this->Groups->delete_group($group_id);
            $this->GroupLoanTypes->delete_group($group_id);
            exit;
        }
    }
}