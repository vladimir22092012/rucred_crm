<?php

class CommunicationsThemesController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            if ($this->request->post('action', 'string')) {
                $methodName = 'action_' . $this->request->post('action', 'string');
                if (method_exists($this, $methodName)) {
                    $this->$methodName();
                }
            }
        }

        $themes = $this->CommunicationsThemes->gets();
        $this->design->assign('themes', $themes);

        return $this->design->fetch('communications_themes.tpl');
    }

    private function action_add_theme()
    {
        $name = $this->request->post('name');

        $this->CommunicationsThemes->add(['name' => $name]);

        exit;
    }

    private function action_update_theme()
    {
        $name = $this->request->post('name');
        $id = $this->request->post('id');

        $themes = $this->CommunicationsThemes->gets(['name' => $name]);

        if (!empty($themes)) {
            echo json_encode(['error' => 'Такая тема уже есть']);
            exit;
        } else {
            $this->CommunicationsThemes->update($id, ['name' => $name]);
            echo json_encode(['success' => 1]);
            exit;
        }
    }

    private function action_delete_theme()
    {
        $id = $this->request->post('id');

        $this->CommunicationsThemes->delete($id);

        echo json_encode(['success' => 1]);
        exit;
    }
}