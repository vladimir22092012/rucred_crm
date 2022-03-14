<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class SmsQueueForSalesCron extends Core
{
    
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
    }
    
    private function run()
    {
        $smsCollection = $this->smssales->get_queue_for_sending_sms(9);

        //var_dump($smsCollection);
        //exit;

        foreach ($smsCollection as $sms) {
            if ($sms->number_of == 1) {
                if (isset($sms->firstname)) {
                    $name = $sms->firstname;
                    
                    $template = $this->sms->get_template(7);
            
                    $message =  preg_replace('/{\\$firstname}/', $name, $template->template, -1, $count);//из шаблонов

                    $result = $this->sms->send($sms->phone, $message);
                    //$this->smssales->send_smssales($sms->phone, $message);
                }
                
                $this->smssales->update_smssales($sms->id, [
                    'number_of' => ($sms->number_of + 1)
                ]);
            }elseif ($sms->number_of == 2) {
                /*
                if (isset($sms->firstname)) {
                    $name = $sms->firstname;

                    $template = $this->sms->get_template(8);
            
                    $message =  preg_replace('/{\\$firstname}/', $name, $template->template, -1, $count);//из шаблонов

                    //$result = $this->sms->send($sms->phone, $message);
                    //$this->smssales->send_smssales($sms->phone, $message);
                }
                
                $this->smssales->update_smssales($sms->id, [
                    'number_of' => ($sms->number_of + 1)
                ]);
                */
            }
        }
    }
    
}
new SmsQueueForSalesCron();