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

        $sort = $this->request->get('sort', 'string');

        if (empty($sort)) {
            $sort = 'id desc';
        }

        $filter['sort'] = $sort;
        $this->design->assign('sort', $sort);

        $loantypes = array();

        if ($this->manager->role == 'employer') {
            $loantype_groups = $this->GroupLoanTypes->get_loantypes_on($this->manager->group_id);

            foreach ($loantype_groups as $loantype){
                $loantypes[] = $this->loantypes->get_loantype($loantype['id']);
            }
        }else{
            $loantypes = $this->loantypes->get_loantypes($filter);
        }

        $this->design->assign('loantypes', $loantypes);

        return $this->design->fetch('offline/loantypes.tpl');
    }
}
