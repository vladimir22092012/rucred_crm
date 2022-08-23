<?php

use Telegram\Bot\Api;
use Viber\Bot;
use Viber\Api\Sender;
use Viber\Client;
use App\Services\MailService;

require __DIR__ . '/../vendor/autoload.php';

class StatusPaymentCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->check_status();
    }

    private function check_status()
    {
        $orders = $this->orders->get_orders(['status' => 10, 'settlement_id' => 3]);

        foreach ($orders as $order){

            $this->db->query("
            SELECT *
            FROM s_transactions
            WHERE order_id = $order->order_id
            AND reference = 'issuance'
            AND reason_code = 0
            ORDER BY id DESC
            LIMIT 1
            ");

            $transaction = $this->db->result();

            if(!empty($transaction)){
                $res = $this->Soap1c->StatusPaymentOrder($transaction->id);

                if(isset($res->return) && $res->return == 'Оплачено'){
                    $this->transactions->update_transaction($transaction->id, ['reason_code' => 1]);
                    $this->orders->update_order($order->order_id, ['status' => 5]);

                    $this->operations->add_operation(array(
                        'contract_id' => $order->contract_id,
                        'type' => 'P2P',
                        'transaction_id' => $transaction->id,
                        'user_id' => $order->user_id,
                        'order_id' => $order->order_id,
                        'amount' => $order->amount,
                        'created' => date('Y-m-d H:i:s')
                    ));

                    $communication_theme = $this->CommunicationsThemes->get(17);


                    $ticket = [
                        'creator' => $order->manager_id,
                        'creator_company' => 2,
                        'client_lastname' => $order->lastname,
                        'client_firstname' => $order->firstname,
                        'client_patronymic' => $order->patronymic,
                        'head' => $communication_theme->head,
                        'text' => $communication_theme->text,
                        'theme_id' => 17,
                        'company_id' => 2,
                        'group_id' => $order->group_id,
                        'order_id' => $order->order_id,
                        'status' => 0
                    ];

                    $ticket_id = $this->Tickets->add_ticket($ticket);

                    $message =
                        [
                            'message' => $communication_theme->text,
                            'ticket_id' => $ticket_id,
                            'manager_id' => $order->manager_id,
                        ];

                    $this->TicketMessages->add_message($message);

                    $cron =
                        [
                            'ticket_id' => $ticket_id,
                            'is_complited' => 0
                        ];

                    $this->NotificationsCron->add($cron);

                    $user_preferred = $this->UserContactPreferred->get($order->user_id);

                    if (!empty($user_preferred)) {
                        $template = $this->sms->get_template(8);

                        foreach ($user_preferred as $preferred) {
                            switch ($preferred->contact_type_id):

                                case 1:
                                    $message = $template->template;
                                    $this->sms->send(
                                        $order->phone_mobile,
                                        $message
                                    );
                                    break;

                                case 2:
                                    $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
                                    $mailService->send(
                                        'rucred@ucase.live',
                                        $order->email,
                                        'RuCred | Уведомление',
                                        "$template->template",
                                        "<h2>$template->template</h2>"
                                    );
                                    break;

                                case 3:
                                    $telegram = new Api($this->config->telegram_token);
                                    $telegram_check = $this->TelegramUsers->get($order->user_id, 0);

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
                                    $viber_check = $this->ViberUsers->get($order->user_id, 0);

                                    if (!empty($viber_check)) {
                                        $bot->getClient()->sendMessage(
                                            (new \Viber\Api\Message\Text())
                                                ->setSender($botSender)
                                                ->setReceiver($viber_check->chat_id)
                                                ->setText($template->template)
                                        );
                                    }
                                    break;

                            endswitch;
                        }
                    }

                }
            }
        }
    }
}

new StatusPaymentCron();