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
                case 'change_permission':
                    $this->action_change_permission();
                    break;
            endswitch;
        } else {

            $docs = $this->Docktypes->get_docs();
            $roles = $this->ManagerRoles->get();
            $permissions = $this->DocksPermissions->get_permissions();

            $this->design->assign('permissions', $permissions);
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

    private function action_change_permission()
    {
        $doc_id = $this->request->post('doc_id');
        $role_id = $this->request->post('role_id');
        $value = $this->request->post('value');

        $permission =
            [
                'docktype_id' => $doc_id,
                'role_id' => $role_id
            ];

        if ($value == 1) {
            $this->DocksPermissions->add_permission($permission);
        }

        if ($value == 0) {
            $this->DocksPermissions->delete_permission($permission);
        }
    }
}