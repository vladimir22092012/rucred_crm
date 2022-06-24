<?php

use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected $token = '5476378779:AAHQmPoqbPB0TW5S8zyo0Ey1abCLZ9hDGq8';

    public function fetch()
    {
        $telegram = new Api($this->token, true);

        $result = $telegram->getWebhookUpdates();

        $text = $result["message"]["text"];
        $chat_id = $result["message"]["chat"]["id"];


        $this->TelegramLogs->add_log(['text' => $chat_id]);

        if($text) {
            if ($text == "/start") {
                $reply = "Добро пожаловать!";
                $telegram->sendMessage(['text' => $reply, 'chat_id' => $chat_id]);
            }
        }
    }
}