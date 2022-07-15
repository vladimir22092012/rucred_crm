<?php
error_reporting(-1);
ini_set('display_errors', 'Off');
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

        $sort = $this->request->get('sort');

        if (empty($sort)) {
            $sort = 'id asc';
        }

        $this->design->assign('sort', $sort);

        $themes = $this->CommunicationsThemes->gets(['sort' => $sort]);
        $this->design->assign('themes', $themes);
        return $this->design->fetch('communications_themes.tpl');
    }

    private function action_add_theme()
    {
        $name = $this->request->post('name');
        $number = $this->request->post('number');
        $head = $this->request->post('head');
        $text = $this->request->post('text');

        $number_check = $this->CommunicationsThemes->gets(['number' => $number]);

        if (!empty($number_check)) {
            echo json_encode(['error' => 'Такой номер уже есть']);
            exit;

        } else {
            echo json_encode(['success' => 1]);

            $theme =
                [
                    'name' => $name,
                    'number' => $number,
                    'head' => $head,
                    'text' => $text
                ];

            $this->CommunicationsThemes->add($theme);
            exit;
        }
    }

    private function action_update_theme()
    {
        $name = $this->request->post('name');
        $number = $this->request->post('number');
        $head = $this->request->post('head');
        $text = $this->request->post('text');
        $need_response = $this->request->post('need_response');
        $id = $this->request->post('theme_id');

        $name_check = $this->CommunicationsThemes->gets(['name' => $name, 'id' => $id]);
        $number_check = $this->CommunicationsThemes->gets(['number' => $number, 'id' => $id]);

        if (!empty($name_check)) {
            echo json_encode(['error' => 'Такая тема уже есть']);
            exit;
        } elseif (!empty($number_check)) {
            echo json_encode(['error' => 'Такой номер уже есть']);
            exit;
        } else {
            $theme =
                [
                    'name' => $name,
                    'number' => $number,
                    'head' => $head,
                    'text' => $text,
                    'need_response' => $need_response
                ];
            $this->CommunicationsThemes->update($id, $theme);

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

    public function action_get_theme()
    {
        $id = $this->request->post('id');
        $theme = $this->CommunicationsThemes->get($id);
        echo json_encode($theme);
        exit;
    }
}