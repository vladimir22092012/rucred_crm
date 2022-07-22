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
            $code->documents = $this->documents->get_documents(['order_id' => $code->order_id, 'asp_flag' => 1]);

            if(empty($code->documents) || $code->code == 0)
                unset($codes[$key]);
            else{
                foreach ($code->documents as $document){
                    if(in_array($document->type, ['INDIVIDUALNIE_USLOVIA', 'GRAFIK_OBSL_MKR', 'DOP_GRAFIK', 'DOP_SOGLASHENIE']))
                        $code->rucred_stamp = true;
                }
            }
        }

        $this->design->assign('codes', $codes);

        return $this->design->fetch('asp_registr.tpl');
    }
}