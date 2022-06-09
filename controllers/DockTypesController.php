<?php

class DockTypesController extends Controller
{
    public function fetch()
    {
        if ($this->request->post('action', 'string')) {
            switch ($this->request->post('action', 'string')) :
                case 'add_dock':
                    $this->action_add_document();
                    break;
            endswitch;
        } else {

            $docs = $this->Docktypes->get_docs();
            $roles = $this->ManagerRoles->get_roles();

            $this->design->assign('roles', $roles);
            $this->design->assign('docs', $docs);
        }

        return $this->design->fetch('docktypes.tpl');
    }

    private function action_add_document()
    {
        $name = $this->request->post('name');
        $templates = $this->request->post('templates');
        $client_visible = $this->request->post('client_visible');
        $online_offline_flag = $this->request->post('online_offline_flag');

        $dock =
            [
                'name' => $name,
                'templates' => $templates,
                'client_visible' => $client_visible,
                'online_offline_flag' => $online_offline_flag
            ];

        $this->Docktypes->add_dock($dock);
        exit;
    }
}