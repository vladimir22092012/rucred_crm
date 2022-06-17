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
        $date_from = date('Y-m-d 00:00:00', strtotime('+3 days'));
        $date_to = date('Y-m-d 23:59:59', strtotime('+3 days'));

        $contracts = $this->contracts->get_contracts(['return_date_from' => $date_from, 'return_date_to' => $date_to]);

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