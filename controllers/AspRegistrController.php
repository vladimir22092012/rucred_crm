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

        $managers = new stdClass();

        foreach($codes as $code){
            $manager = $this->Managers->get_manager($code->manager_id);
            $code->manager = $manager;
            $managers->{$manager->id} = $manager;
            $code->documents = $this->documents->get_documents(['user_id' => $code->user_id]);
        }

        $this->design->assign('managers', $managers);

        $this->design->assign('codes', $codes);
        return $this->design->fetch('asp_registr.tpl');
    }
}