<?php

class GroupsController extends Controller
{
    public function fetch()
    {
        switch ($this->request->post('action', 'string')):
            case 'add_group':
                $this->action_add_group();
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

        $group =
            [
                'name' => $name,
                'number' => $number
            ];

        $this->Groups->add_group($group);
    }
}