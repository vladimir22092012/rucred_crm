<?php

require __DIR__ . '/../vendor/autoload.php';

use Telegram\Bot\Api;

class TelegramApi extends Core
{
    protected $token = '5476378779:AAHQmPoqbPB0TW5S8zyo0Ey1abCLZ9hDGq8';

    public function setWebHook()
    {
        $telegram = new Api($this->token, true);
        $result = $telegram->getWebhookUpdates();
    }
}