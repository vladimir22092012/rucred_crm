<?php


class PaymentsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->make_payment_list();
    }

    public function make_payment_list()
    {
        $order_id = $this->request->post('order_id');
        $order = $this->orders->get_order($order_id);
        $date = date('d.m.Y');
        $time = date('H:i:s');
        $order_created = date('d.m.Y', strtotime($order->date));
        $token = "25c845f063f9f3161487619f630663b2d1e4dcd7";
        $url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/bank';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token ' . $token
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $order->bik_bank]));
        $result = curl_exec($ch);

        if (!empty($result)) {
            $result = json_decode($result);
            $payment_city = $result->suggestions[0]->data->payment_city;
            $correspondent_account = $result->suggestions[0]->data->correspondent_account;
        } else {
            $payment_city = ' ';
            $correspondent_account = ' ';
        }

        $content = '1CClientBankExchange
ВерсияФормата=1.03
Кодировка=Windows
Отправитель=1С:Управление микрофинансовой организацией и кредитным потребительским кооперативом КОРП, редакция 3.0
Получатель=
ДатаСоздания=' . $date . '
ВремяСоздания=' . $time . '
ДатаНачала=' . $date . '
ДатаКонца=' . $date . '
РасчСчет=40701810300000000347
Документ=Платежное поручение
СекцияДокумент=Платежное поручение
Номер=' . $order->uid . '
Дата=' . $date . '
Сумма=' . $order->amount . '
ПлательщикСчет=40701810300000000347
Плательщик=ИНН 9725055162 ООО МКК "РУССКОЕ КРЕДИТНОЕ ОБЩЕСТВО"
ПлательщикИНН=9725055162
Плательщик1=ООО МКК "РУССКОЕ КРЕДИТНОЕ ОБЩЕСТВО"
ПлательщикРасчСчет=40701810300000000347
ПлательщикБанк1=ПАО "РОСДОРБАНК"
ПлательщикБанк2=г. Москва
ПлательщикБИК=044525666
ПлательщикКорсчет=30101810945250000666
ПолучательСчет=' . $order->account_number . '
Получатель=ИНН ' . $order->inn . ' ' . $order->lastname . ' ' . $order->firstname . ' ' . $order->patronymic . '
ПолучательИНН=' . $order->inn . '
Получатель1=' . $order->lastname . ' ' . $order->firstname . ' ' . $order->patronymic . '
ПолучательРасчСчет=' . $order->account_number . '
ПолучательБанк1=' . $order->bank_name . '
ПолучательБанк2=' . $payment_city . '
ПолучательБИК=' . $order->bik_bank . '
ПолучательКорсчет=' . $correspondent_account . '
ВидОплаты=01
Очередность=5
НазначениеПлатежа=Перечисление средств по договору микрозайма №' . $order->uid . ' от ' . $order_created . ' г., Сумма ' . $order->amount . '-00 Без налога (НДС)
НазначениеПлатежа1=Перечисление средств по договору микрозайма №' . $order->uid . ' от ' . $order_created . ' г.,
НазначениеПлатежа2=Сумма ' . $order->amount . '-00
НазначениеПлатежа3=Без налога (НДС)
КонецДокумента
КонецФайла';

        $filename = 'Платежка от ' . date('d-m-Y') . ' перечисление средств по договору микрозайма №' . $order->uid;
        header("Pragma: public");
        header("Content-Type: text/plain; charset=utf-8");
        header("Content-Disposition: attachment; charset=utf-8; filename=$filename.txt");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . strlen($content));
        echo $content;
        exit;
    }
}