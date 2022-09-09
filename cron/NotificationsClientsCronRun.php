<?php

use Telegram\Bot\Api;
use Viber\Bot;
use Viber\Api\Sender;
use App\Services\MailService;

error_reporting(-1);
ini_set('display_errors', 'On');
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

            if(empty($user))
                continue;

            foreach ($user_preferred as $preferred) {
                switch ($preferred->contact_type_id) {

                    case 1:
                        $message = $template->template;
                        $this->sms->send(
                            $user->phone_mobile,
                            $message
                        );

                        $log =
                            [
                                'user_id'    => $cron->user_id,
                                'is_manager' => 0,
                                'type_id'    => 1,
                                'text'       => $message
                            ];

                        $this->NotificationsLogs->add($log);

                        break;

                    case 2:

                        $mail = new PHPMailer(false);

                        //Server settings
                        $mail->isSMTP();                                            //Send using SMTP
                        $mail->Host = 'mail.nic.ru';                          //Set the SMTP server to send through
                        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
                        $mail->Username = 'noreply@re-aktiv.ru';                  //SMTP username
                        $mail->Password = 'HG!_@H#*&!^!HwJSDJ2Wsqgq';             //SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
                        $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        //Recipients
                        $mail->setFrom('noreply@re-aktiv.ru');
                        $mail->addAddress($user->email);     //Add a recipient

                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = 'RuCred | Уведомление';
                        $mail->Body = "<h2>$template->template</h2>";

                        $mail->send();

                        $log =
                            [
                                'user_id'    => $cron->user_id,
                                'is_manager' => 0,
                                'type_id'    => 2,
                                'text'       => $template->template
                            ];

                        $this->NotificationsLogs->add($log);

                        break;

                    case 3:
                        $telegram = new Api($this->config->telegram_token);
                        $telegram_check = $this->TelegramUsers->get($cron->user_id, 0);

                        if(empty($telegram_check->chat_id) || $telegram_check->chat_id == '0')
                            continue;

                        if (!empty($telegram_check)) {
                            $telegram->sendMessage(['chat_id' => $telegram_check->chat_id, 'text' => $template->template]);

                            $log =
                                [
                                    'user_id'    => $cron->user_id,
                                    'is_manager' => 0,
                                    'type_id'    => 3,
                                    'text'       => $template->template
                                ];

                            $this->NotificationsLogs->add($log);
                        }
                        break;

                    case 4:
                        $bot = new Bot(['token' => $this->config->viber_token]);

                        $botSender = new Sender([
                            'name' => 'Whois bot',
                            'avatar' => 'https://developers.viber.com/img/favicon.ico',
                        ]);
                        $viber_check = $this->ViberUsers->get($cron->user_id, 0);

                        if(empty($viber_check->chat_id) || $viber_check->chat_id == '0')
                            continue;

                        if (!empty($viber_check)) {
                            $bot->getClient()->sendMessage(
                                (new \Viber\Api\Message\Text())
                                    ->setSender($botSender)
                                    ->setReceiver($viber_check->chat_id)
                                    ->setText($template->template)
                            );

                            $log =
                                [
                                    'user_id'    => $cron->user_id,
                                    'is_manager' => 0,
                                    'type_id'    => 4,
                                    'text'       => $template->template
                                ];

                            $this->NotificationsLogs->add($log);
                        }
                        break;
                }
            }

            $this->NotificationsClientsCron->update($cron->id, ['is_completed' => 1]);
        }
    }
}

new NotificationsClientsCronRun();