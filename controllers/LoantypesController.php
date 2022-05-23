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
        
        $loantypes = $this->loantypes->get_loantypes();
        $this->design->assign('loantypes', $loantypes);

        return $this->design->fetch('offline/loantypes.tpl');
    }
}
