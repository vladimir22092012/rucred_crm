<?php

use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected $token = '5476378779:AAHQmPoqbPB0TW5S8zyo0Ey1abCLZ9hDGq8';

    public function fetch()
    {
        $telegram = new Api($this->token);

        $result = $telegram->getWebhookUpdates();
        http_response_code(200);

        $text = $result["message"]["text"];
        $chat_id = $result["message"]["chat"]["id"];
        list($command, $token) = explode(' ', $text);

        if($command) {
            if ($command == "/start") {
                $reply = "Добро пожаловать!";
                $telegram->sendMessage(['text' => $reply, 'chat_id' => $chat_id]);
            }
        }
    }
}