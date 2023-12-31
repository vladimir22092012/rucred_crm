<?php

class GroupsController extends Controller
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

        $filter = array();

        if ($this->manager->role == 'employer')
            $filter['employer'] = $this->manager->group_id;

        $groups = $this->Groups->get_groups($filter);

        $this->design->assign('groups', $groups);

        return $this->design->fetch('groups.tpl');
    }

    private function action_add_group()
    {
        $name    = $this->request->post('group_name');
        $blocked = $this->request->post('blocked');

        $last_number = $this->Groups->last_number();

        if ($last_number && $last_number < 10) {
            $last_number += 1;
            $last_number = '0' . $last_number;
        }

        if ($last_number == false) {
            $last_number = '00';
        }
        if ($last_number && $last_number > 10) {
            $last_number += 1;
        }


        $group =
            [
                'name' => $name,
                'number' => $last_number,
                'blocked' => $blocked
            ];

        $group_id = $this->Groups->add_group($group);

        $loantypes = $this->Loantypes->get_loantypes();

        foreach ($loantypes as $loantype) {
            $group =
                [
                    'group_id' => $group_id,
                    'loantype_id' => $loantype->id,
                    'standart_percents' => $loantype->percent,
                    'preferential_percents' => $loantype->profunion,
                    'on_off_flag' => 0,
                ];

            $this->GroupLoanTypes->add_group($group);
        }

        echo json_encode(['success' => 1]);
        exit;
    }

    private function action_update_group()
    {
        $group_id = $this->request->post('group_id', 'integer');
        $group_name = $this->request->post('group_name');
        $number = $this->request->post('number');
        $blocked = $this->request->post('blocked');

        $groups = $this->groups->get_groups(['number' => $number]);
        $group = (array) $this->groups->get_group($group_id);

        if (!empty($groups) && $group['number'] !== $number) {
            echo json_encode(['error' => 'Такой номер уже есть']);
            exit;
        } else {
            $group =
                [
                    'name' => $group_name,
                    'number' => $number,
                    'blocked' => $blocked
                ];

            $this->Groups->update_group($group_id, $group);

            echo json_encode(['success' => 1]);
            exit;
        }
    }

    private function action_delete_group()
    {
        $group_id = $this->request->post('group_id', 'integer');

        $branches = $this->Branches->get_branches_by_group($group_id);

        $this->GroupLoanTypes->delete_group($group_id);

        if (count($branches) > 1) {
            echo 'Ошибка. Количество филиалов более 1';
            exit;
        } else {
            $this->Groups->delete_group($group_id);
            $this->Companies->delete_companies($group_id);
            $this->Branches->delete_branches(['group_id' => $group_id]);
            $this->GroupLoanTypes->delete_group($group_id);
            exit;
        }
    }

    private function action_blocked()
    {
        $group_id = $this->request->post('group_id');
        $value    = $this->request->post('value');

        $this->groups->update_group($group_id, ['blocked' => $value]);
        exit;
    }
}
