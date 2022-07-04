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

        try {
            $bot = new Bot(['token' => $this->apy_key]);

            $bot
                ->onConversation(function ($event) use ($bot, $botSender) {
                    // this event fires if user open chat, you can return "welcome message"
                    // to user, but you can't send more messages!
                    return (new \Viber\Api\Message\Text())
                        ->setSender($botSender)
                        ->setText("Can i help you?");
                })
                ->onText('|whois .*|si', function ($event) use ($bot, $botSender) {
                    // match by template, for example "whois Bogdaan"
                    $bot->getClient()->sendMessage(
                        (new \Viber\Api\Message\Text())
                            ->setSender($botSender)
                            ->setReceiver($event->getSender()->getId())
                            ->setText("I do not know )")
                    );
                })
                ->run();
        } catch (Exception $e) {
            // todo - log exceptions
        }
    }
}