<?php

use Viber\Bot;
use Viber\Api\Sender;
use Viber\Client;

class ViberController extends Controller
{
    protected $message;

    public function fetch()
    {

            $botSender = new Sender([
                'name' => 'Whois bot',
                'avatar' => 'https://developers.viber.com/img/favicon.ico',
            ]);

        $bot = new Bot(['token' => $this->config->viber_token]);

        $bot
            ->onText('|привет|', function ($event) use ($bot, $botSender) {
                $bot->getClient()->sendMessage(
                    (new \Viber\Api\Message\Text())
                        ->setSender($botSender)
                        ->setReceiver($event->getSender()->getId())
                        ->setText("Добро пожаловать!")
                );
            })
            ->onText('|registration|', function ($event) use ($bot, $botSender) {

                $text = $event->getMessage()->getText();
                $text = explode(' ', $text);
                $user_id = $text[1];

                $chat_id = $event->getSender()->getId();
                $user = $this->ViberUsers->get_user_by_chat_id($chat_id);

                if (!empty($user)) {
                    $bot->getClient()->sendMessage(
                        (new \Viber\Api\Message\Text())
                            ->setSender($botSender)
                            ->setReceiver($event->getSender()->getId())
                            ->setText("Такой пользователь уже зарегистрирован")
                    );
                } else {
                    $bot->getClient()->sendMessage(
                        (new \Viber\Api\Message\Text())
                            ->setSender($botSender)
                            ->setReceiver($event->getSender()->getId())
                            ->setText("Вы успешно привязали аккаунт")
                    );

                    $this->ViberUsers->update(['chat_id' => $event->getSender()->getId()], $user_id);
                }
            })
            ->run();
    }

    private function setWebhook()
    {

        $webhookUrl = $this->config->back_url.'/viber';

        try {
            $client = new Client(['token' => $this->config->viber_token]);
            $result = $client->setWebhook($webhookUrl);
            echo "Success!\n";
        } catch (Exception $e) {
            echo '<pre>';
            echo "Error: " . $e . "\n";
        }

    }
}