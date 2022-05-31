<?php

class LoantypesController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            switch ($this->request->post('action', 'string')) :
                case 'delete':
                    $id = $this->request->post('id', 'integer');
                    $this->loantypes->delete_loantype($id);
                    break;
            endswitch;
        }

        $loantypes = array();

        if ($this->manager->role == 'employer') {
            $loantype_groups = $this->GroupLoanTypes->get_loantypes_on($this->manager->group_id);

            foreach ($loantype_groups as $loantype){
                $loantypes[] = $this->loantypes->get_loantype($loantype['id']);
            }
        }else{
            $loantypes = $this->loantypes->get_loantypes();
        }

        $this->design->assign('loantypes', $loantypes);

        return $this->design->fetch('offline/loantypes.tpl');
    }
}
