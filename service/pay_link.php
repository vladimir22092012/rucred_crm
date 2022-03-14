<?php

chdir('..');

require 'autoload.php';

class PayLinkService extends Core
{
    private $response = array();
    
    private $password = 'AX6878EK';
    
    public function __construct()
    {
    	$this->run();
    }
    
    private function run()
    {
    	$number = $this->request->get('number');
        
        if (empty($number))
        {
            $this->response['error'] = 1;
            $this->response['message'] = 'Укажите номер договора';
            
            $this->output();
        }
        
        $password = $this->request->get('password');
        if ($password != $this->password)
        {
            $this->response['error'] = 1;
            $this->response['message'] = 'Укажите пароль обмена';
            
            $this->output();            
        }

        $contract = $this->contracts->get_number_contract($number);
        //var_dump($contract);
        //var_dump($contract->order_id);

        $code = $this->helpers->c2o_encode($contract->id);

        $this->response['success'] = 1;
        $this->response['link'] = $this->config->front_url.'/p/'.$code;

        $this->output();
    }
    
    private function output()
    {
        header('Content-type:application/json');
        echo json_encode($this->response);
        
        exit;
    }
}
new PayLinkService();