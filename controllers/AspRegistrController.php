<?php

class AspRegistrController extends Controller
{
    public function fetch()
    {
        $filter = [];

        if ($this->request->get('sort', 'string')) {
            $filter['sort'] = $this->request->get('sort', 'string');
            $this->design->assign('sort', $filter['sort']);
        }

        $codes = $this->AspCodes->get_codes($filter);

        $users = new stdClass();

        foreach($codes as $code){
            $code->user = $this->users->get_user($code->user_id);
            $code->manager =  $this->managers->get_manager($code->manager_id);
            $code->documents = $this->documents->get_documents(['order_id' => $code->order_id]);
        }

        $this->design->assign('users', $users);

        $this->design->assign('codes', $codes);
        return $this->design->fetch('asp_registr.tpl');
    }
}