<?php

session_start();

class Ajax extends Core
{
    protected $response = array();
    
    protected $user = null;
    
    public function __construct()
    {
        parent::__construct();
    
        if (!empty($_SESSION['user_id'])) {
            $this->user = $this->users->get_user((int)$_SESSION['user_id']);
        }
    }
    
    
    protected function output()
    {
        header("Content-type: application/json; charset=UTF-8");
        header("Cache-Control: must-revalidate");
        header("Pragma: no-cache");
        header("Expires: -1");
        
        echo json_encode($this->response);
    }
}
