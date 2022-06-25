<?php

use Viber\Client;

class ViberController extends Controller
{
    protected $apy_key = '4f668e111aa7defb-b74d69004af9235c-371097ebb1cfa25e';
    protected $webhookUrl = 'https://rucred-dev.ru/viber';

    public function fetch()
    {
        $client = new Client(['token' => $this->apy_key]);
        $result = $client->setWebhook($this->webhookUrl);
        var_dump($result);
    }
}