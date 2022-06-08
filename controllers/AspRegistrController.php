<?php

class AspRegistrController extends Controller
{
    public function fetch()
    {
        $codes = $this->AspCodes->get_codes();

        foreach($codes as $code){
            $code->manager = $this->Managers->get_manager($code->manager_id);
            $code->documents = $this->documents->get_documents(['user_id' => $code->user_id]);
        }

        $this->design->assign('codes', $codes);
        return $this->design->fetch('asp_registr.tpl');
    }
}