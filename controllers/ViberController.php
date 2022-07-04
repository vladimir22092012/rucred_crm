<?php

use Viber\Bot;
use Viber\Api\Sender;

class ViberController extends Controller
{
    protected $apy_key = '4f668e111aa7defb-b74d69004af9235c-371097ebb1cfa25e';

    public function fetch()
    {
        $botSender = new Sender([
            'name' => 'Whois bot',
            'avatar' => 'https://developers.viber.com/img/favicon.ico',
        ]);

        $bot = new Bot(['token' => $this->apy_key]);
        $bot->onText('hello', function ($event) use ($bot, $botSender) {
            $bot->getClient()->sendMessage(
                (new \Viber\Api\Message\Text())
                    ->setSender($botSender)
                    ->setReceiver($event->getSender()->getId())
                    ->setText("hello!")
            );
        })->run();
    }
}