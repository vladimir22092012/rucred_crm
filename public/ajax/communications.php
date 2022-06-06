<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir('../..');
require __DIR__ . '/../../vendor/autoload.php';

class CommunicationsAjax extends Core
{
    private $response = array();
    
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
        
    }
    
    public function run()
    {
    	$action = $this->request->get('action', 'string');
        
        switch ($action):
            
            case 'add':
                $this->action_add_communication();                
            break;
            
            case 'check':
                $this->action_check_communication();                
            break;
            
        endswitch;

        $this->json_output();
        
    }
    
    private function action_add_communication()
    {
        $user_id = $this->request->get('user_id', 'integer');
        $type = $this->request->get('type', 'string');
        $content = (string)$this->request->get('content');
        $from_number = (string)$this->request->get('from_number');
        $to_number = (string)$this->request->get('to_number');
        $mangocall_id = (int)$this->request->get('mangocall_id');
        $yuk = (int)$this->request->get('yuk');
        
        $this->response = $this->communications->add_communication(array(
            'user_id' => $user_id,
            'manager_id' => $this->manager->id,
            'created' => date('Y-m-d H:i:s'),
            'type' => $type,
            'content' => $content,
            'from_number' => $from_number,
            'to_number' => $to_number,
            'mangocall_id' => $mangocall_id,
            'yuk' => $yuk,
        ));
    }
    
    private function action_check_communication()
    {
        $user_id = $this->request->get('user_id', 'integer');
        $is_call = $this->request->get('call', 'integer');
        
        $this->response = (int)$this->communications->check_user($user_id, $is_call);
    }
    
    
    private function json_output()
    {
        header("Content-type: application/json; charset=UTF-8");
        header("Cache-Control: must-revalidate");
        header("Pragma: no-cache");
        header("Expires: -1");	
        
        echo json_encode($this->response);
    }
}
new CommunicationsAjax();