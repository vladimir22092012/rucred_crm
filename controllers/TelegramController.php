<?php

use Telegram\Bot\Api;

class TelegramController extends Controller
{
    public function fetch()
    {
        $telegram = new Api($this->config->telegram_token);

        $result = $telegram->getWebhookUpdates();
        http_response_code(200);
        fastcgi_finish_request();

        $text = $result["message"]["text"];
        $chat_id = $result["message"]["chat"]["id"];
        list($command, $token) = explode(' ', $text);

        if($command) {
            if ($command == "/start") {
                $reply = "Добро пожаловать!";
                $telegram->sendMessage(['text' => $reply, 'chat_id' => $chat_id]);

                $user =
                    [
                        'token' => $token,
                        'chat_id' => $chat_id
                    ];

                $this->TelegramUsers->update($user);
            }
        }
    }
}