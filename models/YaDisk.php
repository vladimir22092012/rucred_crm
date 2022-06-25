<?php

class YaDisk extends Core
{
    protected $token;
    protected $disk;

    public function __construct()
    {
        parent::__construct();
        $this->token = 'AQAAAABcOalaAADLWxIYdswB4kYFjIrgW6xGURU';
        $this->disk = new Arhitector\Yandex\Disk($this->token);
    }

    public function upload_files()
    {
        $resource = $this->disk->getResource('disk:/RC3100 CRM Data/3102 Loans/'.$this->config->root_url.'/test.txt');
        $resource->upload($this->config->root_url.'/test.txt');
        var_dump($resource->toArray());
        exit;
    }
}