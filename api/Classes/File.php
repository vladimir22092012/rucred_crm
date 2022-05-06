<?php

namespace Api\Classes;

class File extends \Core {

    public function __construct()
    {
    	parent::__construct();
        
    }

    public function test() {
   
        dd(1234);
        //TODO: загрузку файлов на удаленный сервер (фронт) по ftp 

    }

}