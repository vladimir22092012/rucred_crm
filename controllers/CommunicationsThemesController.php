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
        $number = $this->request->post('number');

        $number_check = $this->CommunicationsThemes->gets(['number' => $number]);

        if (!empty($number_check)) {
            echo json_encode(['error' => 'Такой номер уже есть']);
            exit;

        } else {
            echo json_encode(['success' => 1]);
            $this->CommunicationsThemes->add(['name' => $name]);
            exit;
        }
    }

    private function action_update_theme()
    {
        $name = $this->request->post('name');
        $number = $this->request->post('number');
        $id = $this->request->post('id');

        $name_check = $this->CommunicationsThemes->gets(['name' => $name, 'id' => $id]);
        $number_check = $this->CommunicationsThemes->gets(['number' => $number, 'id' => $id]);

        if (!empty($name_check)) {
            echo json_encode(['error' => 'Такая тема уже есть']);
            exit;
        } elseif (!empty($number_check)) {
            echo json_encode(['error' => 'Такой номер уже есть']);
            exit;
        } else {
            $this->CommunicationsThemes->update($id, ['name' => $name, 'number' => $number]);
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