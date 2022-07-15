<?php

class SettingsController extends Controller
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

        $settings = $this->SettingsTable->gets();
        $this->design->assign('settings', $settings);

        return $this->design->fetch('settings.tpl');
    }

    private function action_change_type()
    {
        $type = $this->request->post('type');
        $value = $this->request->post('value');

        $this->SettingsTable->update($type, $value);
    }
}
