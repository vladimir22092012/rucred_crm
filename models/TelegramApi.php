<?php

require __DIR__ . '/../vendor/autoload.php';

use Telegram\Bot\Api;

class TelegramApi extends Core
{
    protected $token = '5476378779:AAHQmPoqbPB0TW5S8zyo0Ey1abCLZ9hDGq8';

    public function actions($user_id)
    {
        $telegram = new Api($this->token, true);
        $result = $telegram->getWebhookUpdates();

        $text = $result["message"]["text"];
        $chat_id = $result["message"]["chat"]["id"];

        if($text) {
            if ($text == "/start") {
                $reply = "Добро пожаловать!";
                $telegram->sendMessage(['text' => $reply, 'chat_id' => $chat_id]);

                $user =
                    [
                        'user_id' => $user_id,
                        'chat_id' => $chat_id
                    ];

                $this->TelegramUsers->add_user($user);
            }
        }
    }
}