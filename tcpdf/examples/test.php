<?php
error_reporting(-1);
ini_set('display_errors', 'On');
//============================================================+
// File name   : example_006.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 006 for TCPDF class
//               WriteHTML and RTL support
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: WriteHTML and RTL support
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 006');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavuserif', '', 9);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content
$html = '<table style="page-break-before: always;" width="530" cellspacing="1" cellpadding="7">
    <thead>
        <tr>
            <td style="border: 2.25pt outset #00000a; padding: 0.07in;" valign="top" width="42%" height="165">
                <p align="center"><img src="file:///tmp/lu5123y3r410.tmp/lu5123y3r456_tmp_83eb19a159e3ece5.png"
                        width="143" height="140" name="Рисунок 1" align="bottom" border="0" /></p>
            </td>
            <td style="border: 2.25pt outset #00000a; padding: 0.07in;" width="30%">
                <p style="margin-bottom: 0in;" align="center">ПОЛНАЯ СТОИМОСТЬ ЗАЙМА:</p>
                <p style="margin-top: 0.19in; margin-bottom: 0in;" align="center">________%</p>
                <p style="margin-top: 0.19in;" align="center">__________________ ПРОЦЕНТ(ОВ) ГОДОВЫХ</p>
            </td>
            <td style="border: 2.25pt outset #00000a; padding: 0.07in;" width="28%">
                <p style="margin-bottom: 0in;" align="center">ПОЛНАЯ СТОИМОСТЬ ЗАЙМА:</p>
                <p style="margin-top: 0.19in; margin-bottom: 0in;" align="center">________ РУБЛЕЙ</p>
                <p style="margin-top: 0.19in; margin-bottom: 0in;" align="center">__________________</p>
                <p style="margin-top: 0.19in;" align="center">РУБЛЕЙ ___КОПЕЕК</p>
            </td>
        </tr>
    </thead>
</table>
<p style="margin-bottom: 0.17in; line-height: 100%;" align="center"><br /><br /></p>
<table width="530" cellspacing="0" cellpadding="1">
    <tbody>
        <tr>
            <td>
                <p style="margin-bottom: 0.17in; line-height: 100%;" align="center"> </p>
                <p style="margin-bottom: 0.17in; line-height: 100%;" align="center"><strong>ДОГОВОР ПОТРЕБИТЕЛЬСКОГО
                        ЗАЙМА_№________ ОТ_____</strong></p>
                <p style="margin-bottom: 0.17in; line-height: 100%;" align="center"><strong>ИНДИВИДУАЛЬНЫЕ
                        УСЛОВИЯ</strong></p>
            </td>
        </tr>
        <tr>
            <td style="border: none; padding: 0in;" colspan="6" align="center" width="559">
                <p style="margin-bottom: 0in;" align="justify">Заемщик ___________________________ (Ф.И.О.)_____________
                    (дата рождения), паспорт гражданина Российской Федерации; серия _____ номер ________, выдан
                    ____________ зарегистрирован по адресу: ______________________</p>
                <p align="justify">Кредитор: Общество с ограниченной ответственностью Микрокредитная компания "На
                    личное+" (сокращенное наименование ООО МКК "На личное+"), <span
                        style="text-decoration: underline;">ОГРН 1196313019066</span>, созданное и действующее в
                    соответствии с законодательством Российской Федерации. Адрес: 443058, Самарская обл., г. Самара, ул.
                    Победы, 86, оф.2.1</p>
            </td>
        </tr>
        <tr>
            <td style="border: none; padding: 0in;" width="539">
                <p style="margin-bottom: 0in;" align="justify"> </p>
                <p align="justify"><strong>Максимальный размер процентов, неустойки (штрафы, пени), иных мер
                        ответственности по договору, </strong><span style="text-decoration: underline;">а также платежей
                        за услуги, оказываемые кредитором заемщику за отдельную плату по договору потребительского
                        займа</span><strong> не может превышать полуторократного размера суммы займа. С даты
                        возникновения просрочки исполнения обязательств Заемщика по возврату суммы займа Кредитор вправе
                        начислять Заемщику неустойку (штрафы, пени) и применять иные меры ответственности только на не
                        погашенную Заемщиком часть суммы основного долга.</strong></p>
            </td>
        </tr>
        <tr>
            <td style="border: none; padding: 0in;" width="539">
                <p align="justify"> </p>
            </td>
        </tr>
        
    </tbody>
</table>
<p style="margin-bottom: 0.17in; line-height: 100%;" align="center"><br /><br /></p>
<table width="665" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" colspan="3" valign="top" width="659">
                <p align="center"> </p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">№ п/п</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p align="center">Условие</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p align="center">Содержание условия</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">1</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Сумма займа</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in;">00,00 руб. (00.00 рублей 00 копеек)</p>
                <p style="margin-left: 0.04in; margin-right: 0.04in;"> </p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">2</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Срок действия договора, срок возврата займа</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p>До _____день,_____мес., _______ год (включительно)</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">3</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Валюта, в которой предоставляется кредит заем</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Российские рубли</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">4</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Процентная ставка ) в процентах годовых</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p>365,000 (Триста шестьдесят пять целых ноль тысячных) процентов годовых (1 % в день).</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">5</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Порядок определения курса иностранной валюты при
                    переводе денежных средств кредитором третьему лицу, указанному заемщиком</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Не применимо</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">5.1</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Указание на изменение суммы расходов заемщика при
                    увеличении используемой в договоре переменной процентной ставки потребительского займа на один
                    процентный пункт начиная со второго очередного платежа на ближайшую дату после предполагаемой даты
                    заключения договора</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Не применимо</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">6</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Количество, размер и периодичность (сроки)
                    платежей заемщика по договору или порядок определения этих платежей</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p>Сумма займа и процентов подлежат оплате единовременным платежом в срок, указанный в п. 2
                    Индивидуальных условий.<br />Размер платежа к моменту возврата займа 00,00 руб. (00.00 рублей 00
                    копеек)</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">7</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Порядок изменения количества, размера и
                    периодичности (сроков) платежей заемщика при частичном досрочном возврате займа</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in;" align="justify">Проценты начисляются на оставшуюся непогашенную часть
                    суммы займа со дня, следующего за днем частичного погашения. Оставшаяся задолженность в полном
                    объеме должна быть погашена в дату, указанную в п. 2 Индивидуальных условий</p>
                <p style="margin-left: 0.04in; margin-right: 0.04in;"> </p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">8</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Способы исполнения заемщиком обязательств по
                    договору по месту нахождения заемщика</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in;">1. Внесение наличных денежных средств через терминалы<br />2. Безналичным
                    платежом на расчетный счет р/с 40701810754400000266</p>
                <p style="margin-bottom: 0in;">в ПАО Сбербанк, к/с 30101810200000000607, БИК 043601607</p>
                <p style="margin-bottom: 0in;">3. Оплата банковской картой в личном кабинете на официальном сайте
                    Кредитора</p>
                <p style="margin-left: 0.04in; margin-right: 0.04in;">https://my.nalichnoe.com</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">8.1</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Бесплатный способ исполнения заемщиком
                    обязательств по договору</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in;">В безналичном порядке с банковского счета Заемщика на расчетный счет
                    Кредитора.</p>
                <p> </p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">9</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Обязанность заемщика заключить иные договоры</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Не применимо</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">10</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Обязанность заемщика по предоставлению обеспечения
                    исполнения обязательств по договору и требования к такому обеспечению</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p>Не применимо</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">11</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Цели использования заемщиком потребительского
                    займа</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Не применимо</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">12</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Ответственность заемщика за ненадлежащее
                    исполнение условий договора, размер неустойки (штрафа, пени) или порядок их определения</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in; background: #ffffff;" align="justify">В случае ненадлежащего исполнения
                    Заемщиком обязательств Заемщик выплачивает неустойку в размере не более 20% (Двадцати процентов)
                    годовых, при этом процент, указанный в п.4 Индивидуальных условий начисляется за соответствующий
                    период нарушения обязательств.</p>
                <p style="margin-bottom: 0in;"> </p>
                <p style="margin-left: 0.04in; margin-right: 0.04in;"> </p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44" height="1">
                <p align="center">13</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Условие об уступке кредитором третьим лицам прав
                    (требований) по договору</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in;">1. Кредитор имеет право на полную или частичную уступку прав (требований)
                    по Договору юридическому лицу, осуществляющему профессиональную деятельность по предоставлению
                    потребительских займов; юридическому лицу, осуществляющему деятельность по возврату просроченной
                    задолженности физических лиц в качестве основного вида деятельности; специализированному финансовому
                    обществу(Далее по тексту - Третьи лица.)</p>
                <p style="margin-bottom: 0in;" align="justify">2. Кредитор не вправе переуступить право на взыскание
                    задолженность по Договору Третьим лицам.</p>
                <p style="text-indent: 0.39in; margin-bottom: 0in;" align="justify"> </p>
                <p style="margin-top: 0.19in;"> </p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">14</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Согласие заемщика с общими условиями договора</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in;">Заемщик подтверждает, что:</p>
                <p style="margin-bottom: 0in;">- ознакомлен, понимает, полностью согласен, а также обязуется соблюдать
                    положения Общих условий договора.</p>
                <p>Общие условия предоставления займов размещены на официальном сайте Кредитора
                    https://my.nalichnoe.com, а также во всех офисах Кредитора</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">15</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Услуги, оказываемые кредитором заемщику за
                    отдельную плату и необходимые для заключения договора, их цена или порядок ее определения, а также
                    согласие заемщика на оказание таких услуг</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Не применимо</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">16</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-left: 0.04in; margin-right: 0.04in;">Способ обмена информацией между кредитором и
                    заемщиком</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in;">Способы обмена информации, используемые Кредитором, для уведомления
                    Заемщика:</p>
                <p style="margin-bottom: 0in;">простое письмо (ФГУП «Почта России»)</p>
                <p style="margin-bottom: 0in;">- смс сообщения ;</p>
                <p style="margin-bottom: 0in;">- голосовые и иные сообщения, передаваемые по сетям радиотелефонной
                    связи;</p>
                <p style="margin-bottom: 0in;">- сообщения на электронную почту, адрес которой, Заемщик указал в
                    анкете-заявлении;</p>
                <p style="margin-bottom: 0in;">текстовые, мультимедийные и иные сообщения, передаваемые с использованием
                    таких общедоступных программных средств как: WhatsApp, Viber, а также мессенджеров (программных
                    модулей)</p>
                <p style="margin-bottom: 0in;" align="justify">Способы обмена информации, используемые Заемщиком, для
                    уведомления Кредитора:</p>
                <p style="margin-bottom: 0in;" align="justify">- простое письмо (ФГУП «Почта России»);</p>
                <p style="margin-bottom: 0in;" align="justify">- личное обращение в офис Кредитора;</p>
                <p style="margin-bottom: 0in;" align="justify">- посредством телефонного обращения, по номеру телефона,
                    указанному на официальном сайте Кредитора https://my.nalichnoe.com</p>
                <p style="margin-left: 0.04in; margin-right: 0.04in;"> </p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">17</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p>Кредитор вправе:</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="text-indent: 0.39in;" align="justify">Изменять Общие условия договора при условии, что это не
                    повлечет за собой возникновение новых или увеличение размера существующих денежных обязательств
                    Заемщика по договору. Новая редакция Общих условий Договора становится обязательной для сторон на
                    следующей день после официального опубликования на официальном сайте Кредитора
                    https://my.nalichnoe.com</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">18</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p style="margin-bottom: 0in;">Порядок уведомления Заемщика о наличии просроченной задолженности по
                    Договору</p>
                <p style="margin-left: 0.04in; margin-right: 0.04in;"> </p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="margin-bottom: 0in;" align="justify">Информация о наличии просроченной задолженности по
                    Договору направляется Заемщику в срок не позднее 7 (Семи) календарных дней с даты возникновения
                    просроченной задолженности одним из ниже перечисленных способов:</p>
                <p style="margin-top: 0.19in; margin-bottom: 0in;" align="justify">- голосовое сообщение либо
                    смс-сообщение на номер телефона, указанный Заемщиком, как контактный;</p>
                <p style="margin-top: 0.19in;" align="justify">- на электронную почту, адрес которой Заемщик указал в
                    анкете-заявлении.</p>
            </td>
        </tr>
        <tr valign="top">
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                <p align="center">19</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                <p>Предоставление информации после заключения Договора</p>
            </td>
            <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                <p style="text-indent: 0.38in; margin-bottom: 0in;" align="justify">После предоставления займа Заемщик
                    вправе получать по запросу один раз в месяц бесплатно и любое количество раз за плату следующую
                    информацию:</p>
                <p style="text-indent: 0.38in; margin-bottom: 0in;" align="justify">1) размер текущей задолженности по
                    Договору;</p>
                <p style="text-indent: 0.38in; margin-bottom: 0in;" align="justify">2) даты и размеры произведенных за
                    предшествующий месяц платежей и предстоящего платежа заемщика по Договору;</p>
                <p style="text-indent: 0.38in; margin-bottom: 0in;" align="justify">3) иные сведения, указанные в
                    Договоре.</p>
                <p>Плата за предоставление информации осуществляется в размере в соответствии с тарифами, действующими
                    на дату предоставления информации.</p>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-indent: 0.39in; margin-bottom: 0in; line-height: 100%;" align="justify"> </p>
<table width="397" cellspacing="0" cellpadding="7">
    <tbody>
        <tr>
            <td style="border: none; padding: 0in;" valign="bottom" width="383" height="122">
                <table width="100%" cellspacing="0" cellpadding="7">
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #000000; padding: 0in 0.08in;" valign="top" width="100%"
                                height="98">
                                <p style="margin-bottom: 0in;">Подписано с использованием ПЭП</p>
                                <p style="margin-bottom: 0in;">Иванова Ивана Ивановича</p>
                                <p style="margin-bottom: 0in;"> </p>
                                <p style="margin-bottom: 0in;">_____"_____________________" 2020</p>
                                <p style="margin-bottom: 0in;"> </p>
                                <p style="margin-bottom: 0in;"> </p>
                                <p style="margin-bottom: 0in;">Телефон ??? (если возможно)</p>
                                <p style="margin-bottom: 0in;"> </p>
                                <p style="margin-bottom: 0in;">СМС-код</p>
                                <p> </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>';

$pdf->writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='');

// output the HTML content
//$pdf->writeHTML($html, true, false, true, false, '');


// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_006.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
