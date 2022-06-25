<?php

use Viber\Client;
exit(123);

class ViberController extends Controller
{
    protected $apy_key;
    protected $webhookUrl;

    public function __construct()
    {
        parent::__construct();
        $this->apy_key = '4f668e111aa7defb-b74d69004af9235c-371097ebb1cfa25e';
        $this->webhookUrl = 'https://rucred-dev.ru/viber';
    }

    public function fetch()
    {
        try {
            $client = new Client([ 'token' => $this->apy_key ]);
            $result = $client->setWebhook($this->webhookUrl);
            echo "Success!\n";
        } catch (Exception $e) {
            echo "Error: ". $e->getError() ."\n";
        }
    }
}