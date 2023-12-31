<?php

class AspRegistrController extends Controller
{
    public function fetch()
    {
        $filter = [];

        if ($this->request->get('sort', 'string')) {
            $filter['sort'] = $this->request->get('sort', 'string');
        }else{
            $filter['sort'] = 'id desc';
        }

        $this->design->assign('sort', $filter['sort']);

        $codes = $this->AspCodes->get_codes($filter);

        foreach($codes as $key => $code){
            $code->user = $this->users->get_user($code->user_id);
            $code->manager =  $this->managers->get_manager($code->manager_id);
            $code->documents = $this->documents->get_documents(['order_id' => $code->order_id, 'asp_flag' => $code->id]);

            if($code->type == 'rucred_sms')
                $code->documents = $this->documents->get_documents(['order_id' => $code->order_id, 'rucred_asp' => $code->id]);

            if(empty($code->documents))
                unset($codes[$key]);
        }

        $this->design->assign('codes', $codes);

        return $this->design->fetch('asp_registr.tpl');
    }
}