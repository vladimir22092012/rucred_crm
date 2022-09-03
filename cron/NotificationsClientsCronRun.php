<?php

use Telegram\Bot\Api;
use Viber\Bot;
use Viber\Api\Sender;
use App\Services\MailService;

error_reporting(-1);
ini_set('display_errors', 'Off');
chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class NotificationsClientsCronRun extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $crons = $this->NotificationsClientsCron->gets();

        if (empty($crons))
            die();

        foreach ($crons as $cron) {
            $user_preferred = $this->UserContactPreferred->get($cron->user_id);

            if (empty($user_preferred))
                continue;

            $template = $this->sms->get_template($cron->template_id);
            $user = $this->users->get_user($cron->user_id);

            foreach ($user_preferred as $preferred) {
                switch ($preferred->contact_type_id) {

                    case 1:
                        $message = $template->template;
                        $this->sms->send(
                            $user->phone_mobile,
                            $message
                        );
                        break;

                    case 2:
                        $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
                        $mailService->send(
                            'rucred@ucase.live',
                            $user->email,
                            'RuCred | Уведомление',
                            "$template->template",
                            "<h2>$template->template</h2>"
                        );
                        break;

                    case 3:
                        $telegram = new Api($this->config->telegram_token);
                        $telegram_check = $this->TelegramUsers->get($user->id, 0);

                        if (!empty($telegram_check)) {
                            $telegram->sendMessage(['chat_id' => $telegram_check->chat_id, 'text' => $template->template]);
                        }
                        break;

                    case 4:
                        $bot = new Bot(['token' => $this->config->viber_token]);

                        $botSender = new Sender([
                            'name' => 'Whois bot',
                            'avatar' => 'https://developers.viber.com/img/favicon.ico',
                        ]);
                        $viber_check = $this->ViberUsers->get($user->id, 0);

                        if (!empty($viber_check)) {
                            $bot->getClient()->sendMessage(
                                (new \Viber\Api\Message\Text())
                                    ->setSender($botSender)
                                    ->setReceiver($viber_check->chat_id)
                                    ->setText($template->template)
                            );
                        }
                        break;
                }
            }

            $this->NotificationsClientsCron->update($cron->id, ['is_completed' => 1]);
        }
    }
}

new NotificationsClientsCronRun();