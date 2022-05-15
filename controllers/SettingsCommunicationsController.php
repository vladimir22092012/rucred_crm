<?php

class SettingsCommunicationsController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            $this->settings->sms_limit_communications = $this->request->post('sms_limit_communications');
            $this->settings->call_limit_communications = $this->request->post('call_limit_communications');

            $this->settings->workday_worktime = $this->request->post('workday_worktime');
            $this->settings->holiday_worktime = $this->request->post('holiday_worktime');
        }
  
        return $this->design->fetch('settings_communications.tpl');
    }
}
