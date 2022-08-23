<?php

use Telegram\Bot\Api;
use Viber\Bot;
use Viber\Api\Sender;
use Viber\Client;
use App\Services\MailService;

error_reporting(-1);
ini_set('display_errors', 'Off');
date_default_timezone_set('Europe/Moscow');
chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class NotificationsCronRun extends Core
{
    protected $telegram;


    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $crons = $this->NotificationsCron->gets();

        foreach ($crons as $cron) {
            $ticket = $this->tickets->get_ticket($cron->ticket_id);

            if (in_array($ticket->theme_id, [8, 17, 20, 22, 24, 31])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'employer']);
            }

            if (in_array($ticket->theme_id, [11, 13, 18, 38])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'underwriter']);
            }

            if (in_array($ticket->theme_id, [12, 37])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => 'middle']);
            }

            if (in_array($ticket->theme_id, [23, 25, 26, 34, 35])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => ['underwriter', 'middle']]);
            }

            if (in_array($ticket->theme_id, [27, 28, 29, 30, 32, 33])) {
                $managers = $this->managers->get_managers(['group_id' => $ticket->group_id, 'role' => ['underwriter', 'middle', 'admin']]);
            }

            foreach ($managers as $manager) {
                if ($manager->telegram_note == 1) {
                    $this->telegram_note($manager->id, $ticket, 1);
                }
                if ($manager->viber_note == 1) {
                    $this->viber_note($manager->id, $ticket, 1);
                }
                if ($manager->sms_note == 1) {
                    $this->sms_note($manager->phone, $ticket);
                }
                if ($manager->email_note == 1) {
                    $this->mail_note($manager->email, $ticket);
                }
            }

            $this->NotificationsCron->update($cron->id, ['is_complited' => 1]);
        }
    }

    private function telegram_note($manager_id, $ticket, $is_manager)
    {
        $telegram = new Api($this->config->telegram_token);
        $telegram_check = $this->TelegramUsers->get($manager_id, $is_manager);

        if (!empty($telegram_check)) {
            $telegram->sendMessage(['chat_id' => $telegram_check->chat_id, 'text' => $ticket->text]);
        }
    }

    private function viber_note($manager_id, $ticket, $is_manager)
    {
        $bot = new Bot(['token' => $this->config->viber_token]);

        $botSender = new Sender([
            'name' => 'Whois bot',
            'avatar' => 'https://developers.viber.com/img/favicon.ico',
        ]);

        $viber_check = $this->ViberUsers->get($manager_id, $is_manager);

        if (!empty($viber_check)) {

            try {
                $bot->getClient()->sendMessage(
                    (new \Viber\Api\Message\Text())
                        ->setSender($botSender)
                        ->setReceiver($viber_check->chat_id)
                        ->setText($ticket->text)
                );
            } catch (Exception $e) {
                var_dump($e);
            }
        }
    }

    private function sms_note($phone, $ticket)
    {
        $phone = preg_replace('![^0-9]+!', '', $phone);
        $message = $ticket->text;
        $this->sms->send(
            $phone,
            $message
        );
    }

    private function mail_note($mail, $ticket)
    {
        $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
        $mailService->send(
            'rucred@ucase.live',
            $mail,
            'RuCred | Уведомление',
            "$ticket->head",
            "<h2>$ticket->text</h2>"
        );
    }
}

new NotificationsCronRun();