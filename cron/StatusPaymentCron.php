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

        foreach ($orders as $order) {

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

            if (!empty($transaction)) {
                $res = $this->Soap1c->StatusPaymentOrder($transaction->id);

                if (isset($res->return) && $res->return == 'Оплачено') {
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

                    $cron =
                        [
                            'template_id' => 8,
                            'user_id' => $order->user_id,
                        ];

                    $this->NotificationsClientsCron->add($cron);

                    $this->design->assign('order', $order);
                    $documents = $this->documents->get_documents(['order_id' => $order->order_id]);
                    $docs_email = [];

                    foreach ($documents as $document) {
                        if (in_array($document->type, ['INDIVIDUALNIE_USLOVIA', 'GRAFIK_OBSL_MKR', 'INDIVIDUALNIE_USLOVIA'])) {
                            $docs_email[$document->type] = $document->hash;
                        }
                    }

                    $individ_encrypt = $this->config->back_url . '/online_docs?id=' . $docs_email['INDIVIDUALNIE_USLOVIA'];
                    $graphic_encrypt = $this->config->back_url . '/online_docs?id=' . $docs_email['GRAFIK_OBSL_MKR'];

                    $this->design->assign('individ_encrypt', $individ_encrypt);
                    $this->design->assign('graphic_encrypt', $graphic_encrypt);

                    $contracts = $this->contracts->get_contracts(['order_id' => $order->order_id]);
                    $group = $this->groups->get_group($order->group_id);
                    $company = $this->companies->get_company($order->company_id);

                    if (!empty($contracts)) {
                        $count_contracts = count($contracts);
                        $count_contracts = str_pad($count_contracts, 2, '0', STR_PAD_LEFT);
                    } else {
                        $count_contracts = '01';
                    }

                    $loantype = $this->Loantypes->get_loantype($order->loan_type);

                    $uid = "$group->number$company->number $loantype->number $order->personal_number $count_contracts";
                    $this->design->assign('uid', $uid);

                    $fetch = $this->design->fetch('email/approved.tpl');

                    $mailService = new MailService($this->config->mailjet_api_key, $this->config->mailjet_api_secret);
                    $mailService->send(
                        'rucred@ucase.live',
                        $order->email,
                        'RuCred | Ваш займ успешно выдан',
                        'Поздравляем!',
                        $fetch
                    );

                } else {
                    echo '<pre>';
                    var_dump($res);
                    exit;
                }
            }
        }
    }
}

new StatusPaymentCron();