<?php

use Telegram\Bot\Api;
use Viber\Bot;
use Viber\Api\Sender;
use PHPMailer\PHPMailer\PHPMailer;

error_reporting(-1);
ini_set('display_errors', 'Off');
date_default_timezone_set('Europe/Moscow');
chdir(dirname(__FILE__) . '/../');

require __DIR__ . '/../vendor/autoload.php';

class NotificationsCronRun extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $crons = $this->NotificationsCron->gets();

        if (empty($crons))
            die();

        foreach ($crons as $cron) {
            $ticket = $this->tickets->get_ticket($cron->ticket_id);

            if (empty($ticket))
                continue;


            $managers_permissions = $this->ManagersCommunicationsIn->get($ticket->theme_id);

            if (empty($managers_permissions))
                continue;

            $roles_id = [];

            foreach ($managers_permissions as $permission) {
                $roles_id[] = $permission->role_id;
            }

            $roles_name = [];
            $roles = $this->ManagerRoles->get();

            foreach ($roles as $role) {
                foreach ($roles_id as $id) {
                    if ($role->id == $id)
                        $roles_name[] = $role->name;
                }
            }

            $managers = $this->managers->get_managers(['role' => $roles_name]);

            foreach ($managers as $manager) {

                if (isset($manager->blocked) && $manager->blocked == 1) {
                    continue;
                }

                if ($manager->role == 'employer')
                {
                    $skip = 1;

                    $managers_companies = $this->ManagersEmployers->get_records($manager->id);

                    if(empty($managers_companies))
                        continue;

                    foreach ($managers_companies as $company_id => $company_name)
                        if($ticket->company_id == $company_id)
                            $skip = 0;

                    if($skip == 1)
                        continue;
                }

                if ($manager->telegram_note == 1 && $this->settings->messenger_notify_manager_status) {
                    $this->telegram_note($manager->id, $ticket, 1);
                }
                if ($manager->viber_note == 1 && $this->settings->messenger_notify_manager_status) {
                    $this->viber_note($manager->id, $ticket, 1);
                }
                if ($manager->sms_note == 1 && $this->settings->phone_notify_manager_status == true) {
                    $this->sms_note($manager->id, $manager->phone, $ticket);
                }
                if ($manager->email_note == 1 && $this->settings->mail_notify_manager_status == true) {
                    $this->mail_note($manager->id, $manager->email, $ticket);
                }
            }

            $this->NotificationsCron->update($cron->id, ['is_complited' => 1]);
        }
    }

    private function telegram_note($manager_id, $ticket, $is_manager)
    {
        try {
            $telegram = new Api($this->config->telegram_token);
            $telegram_check = $this->TelegramUsers->get($manager_id, $is_manager);

            if (!empty($telegram_check)) {
                $telegram->sendMessage(['chat_id' => $telegram_check->chat_id, 'text' => $ticket->text]);

                $log =
                    [
                        'user_id' => $manager_id,
                        'is_manager' => 1,
                        'type_id' => 3,
                        'text' => $ticket->text
                    ];

                $this->NotificationsLogs->add($log);
            }
        } catch (Exception $e) {

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

                $log =
                    [
                        'user_id' => $manager_id,
                        'is_manager' => 1,
                        'type_id' => 4,
                        'text' => $ticket->text
                    ];

                $this->NotificationsLogs->add($log);

            } catch (Exception $e) {
                var_dump($e);
            }
        }
    }

    private function sms_note($manager_id, $phone, $ticket)
    {
        $phone = preg_replace('![^0-9]+!', '', $phone);
        $message = $ticket->text;
        $this->sms->send(
            $phone,
            $message
        );

        $log =
            [
                'user_id' => $manager_id,
                'is_manager' => 1,
                'type_id' => 1,
                'text' => $ticket->text
            ];

        $this->NotificationsLogs->add($log);
    }

    private function mail_note($manager_id, $email, $ticket)
    {

        $order = $this->orders->get_order($ticket->order_id);
        $fio = "$order->lastname $order->firstname $order->patronymic";
        $loantype = $this->loantypes->get_loantype($order->loan_type);

        $message = '';
        $message .= "<h2>$ticket->text</h2><br><br>";
        $message .= '<h3>Номер тикета: ' . str_pad($ticket->id, 6, 0, STR_PAD_LEFT) . '</h3>';
        $message .= '<h3>Номер заявки: ' . $order->order_id . '</h3>';
        $message .= '<h3>Дата заявки: ' . date('d.m.Y', strtotime($order->date)) . '</h3>';
        $message .= '<h3>ФИО клиента: ' . $fio . '</h3>';
        $message .= '<h3>Тариф: ' . $loantype->name . '</h3>';
        $message .= '<h3>Сумма: ' . $order->amount . '</h3>';
        $message .= "Ссылка на тикет: " . $this->config->back_url . '/ticket/' . $ticket->id . '<br><br>';


        $mail = new PHPMailer(false);

        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'mail.nic.ru';                          //Set the SMTP server to send through
        $mail->CharSet = 'UTF-8';
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'noreply@re-aktiv.ru';                  //SMTP username
        $mail->Password = 'HG!_@H#*&!^!HwJSDJ2Wsqgq';             //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
        $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('noreply@re-aktiv.ru');
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'RuCred | Уведомление';
        $mail->Body = $message;

        $mail->send();

        $log =
            [
                'user_id' => $manager_id,
                'is_manager' => 1,
                'type_id' => 2,
                'text' => $ticket->text
            ];

        $this->NotificationsLogs->add($log);
    }
}

new NotificationsCronRun();
