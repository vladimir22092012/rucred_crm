<?php

class SettingsController extends Controller
{
    public function fetch()
    {

        if ($this->request->method('post')) {
            
            $this->settings->b2p_mode = $this->request->post('b2p_mode', 'string');
            
//            $this->settings->loan_min_summ = $this->request->post('loan_min_summ', 'integer');
//            $this->settings->loan_default_summ = $this->request->post('loan_default_summ', 'integer');
//            $this->settings->loan_max_summ = $this->request->post('loan_max_summ', 'integer');
//
//            $this->settings->loan_min_period = $this->request->post('loan_min_period', 'integer');
//            $this->settings->loan_default_period = $this->request->post('loan_default_period', 'integer');
//            $this->settings->loan_max_period = $this->request->post('loan_max_period', 'integer');
//            
//            $this->settings->loan_default_percent = (float)str_replace(',', '.', $this->request->post('loan_default_percent'));
//            $this->settings->loan_peni = (float)str_replace(',', '.', $this->request->post('loan_peni'));
//            $this->settings->loan_charge_percent = (float)str_replace(',', '.', $this->request->post('loan_charge_percent'));
//            
//            $this->settings->prolongation_period = $this->request->post('prolongation_period', 'integer');
//            $this->settings->prolongation_amount = $this->request->post('prolongation_amount', 'integer');
//        
//            $this->settings->cession_period = $this->request->post('cession_period', 'integer');
//            $this->settings->cession_amount = $this->request->post('cession_amount', 'integer');
//            
//            $this->settings->report_email = $this->request->post('report_email');
        } else {
        }
        

  
        return $this->design->fetch('settings.tpl');
    }
}
