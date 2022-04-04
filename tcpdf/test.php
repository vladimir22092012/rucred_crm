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

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content
$html = '<table width="665">
    <tr>
        <td>
            <table style="page-break-before: always;" width="100%" cellspacing="1" cellpadding="7">
                <colgroup>
                    <col width="108*" />
                    <col width="77*" />
                    <col width="72*" />
                </colgroup>
                <thead>
                    <tr>
                        <td style="border: 2.25pt outset #00000a; padding: 0.07in;" valign="top" width="42%"
                            height="165">
                            <p align="center"><img
                                    src="file:///home/ini1990/Documents/%D0%B4%D0%BE%D0%BF%D0%BE%D0%BB%D0%BD%D0%B8%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5_%D1%81%D0%BE%D0%B3%D0%BB%D0%BB%D0%B0%D1%88%D0%B5%D0%BD%D0%B8%D0%B5_%D0%BE_%D0%BF%D1%80%D0%BE%D0%BB%D0%BE%D0%BD%D0%B3%D0%B0%D1%86%D0%B8%D0%B8_1_html_83eb19a159e3ece5.png"
                                    width="143" height="140" name="Рисунок 1" align="bottom" border="0" /></p>
                        </td>
                        <td style="border: 2.25pt outset #00000a; padding: 0.07in;" width="30%">
                            <p style="margin-bottom: 0in;" align="center"><span style="font-size: medium;">ПОЛНАЯ
                                    СТОИМОСТЬ ЗАЙМА:</span></p>
                            <p style="margin-top: 0.19in; margin-bottom: 0in;" align="center"><span
                                    style="font-size: medium;">________%</span></p>
                            <p style="margin-top: 0.19in;" align="center"><span
                                    style="font-size: medium;">__________________ ПРОЦЕНТ(ОВ) ГОДОВЫХ</span></p>
                        </td>
                        <td style="border: 2.25pt outset #00000a; padding: 0.07in;" width="28%">
                            <p style="margin-bottom: 0in;" align="center"><span style="font-size: medium;">ПОЛНАЯ
                                    СТОИМОСТЬ ЗАЙМА:</span></p>
                            <p style="margin-top: 0.19in; margin-bottom: 0in;" align="center"><span
                                    style="font-size: medium;">________ РУБЛЕЙ</span></p>
                            <p style="margin-top: 0.19in; margin-bottom: 0in;" align="center"><span
                                    style="font-size: medium;">__________________</span></p>
                            <p style="margin-top: 0.19in;" align="center"><span style="font-size: medium;">РУБЛЕЙ
                                    ___КОПЕЕК</span></p>
                        </td>
                    </tr>
                </thead>
            </table>
            <p style="margin-bottom: 0.17in; line-height: 100%;" align="center"><span
                    style="font-size: medium;"><strong>ДОПОЛНИТЕЛЬНОЕ СОГЛАШЕНИЕ К ДОГОВОРУ ПОТРЕБИТЕЛЬСКОГО ЗАЙМА
                        №</strong></span></p>
            <table style="height: 858px;" width="760" cellspacing="0" cellpadding="1">
                <colgroup>
                    <col width="11" />
                    <col width="9" />
                    <col width="9" />
                    <col width="10" />
                    <col width="607" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="2" />
                    <col width="3" />
                </colgroup>
                <tbody>
                    <tr style="height: 728px;">
                        <td style="padding: 0in; height: 728px; width: 705.983px;" colspan="5">
                            <p style="margin-bottom: 0in;" align="justify"><span style="font-size: medium;">Заемщик:
                                    ___________________________ (Ф.И.О.)_____________ (дата рождения), паспорт
                                    гражданина Российской Федерации; серия _____ номер ________, выдан ____________
                                    зарегистрирован по адресу: ______________________ </span></p>
                            <p style="margin-top: 0.19in; margin-bottom: 0in;"><span
                                    style="font-size: medium;">Кредитор: Общество с ограниченной ответственностью
                                    микрокредитная компания ""На личное+"" (сокращенное наименование ООО МКК "На
                                    личное+"), <span style="text-decoration: underline;">ОГРН
                                        1196313019066</span>,созданное и действующее в соответствии с законодательством
                                    Российской Федерации. Адрес: 443058, Самарская обл. г. Самара, ул. Победы 86,
                                    оф.2.1, <span style="color: #000000;">заключили настоящее дополнительное соглашение
                                        (далее Соглашение) о нижеследующем:</span></span></p>
                            <p style="margin-bottom: 0in;" align="justify">&nbsp;</p>
                            <p style="margin-bottom: 0in;" align="justify"><span style="font-size: medium;">1. <span
                                        style="text-decoration: line-through;">Изложить п. 1 в следующей
                                        редакции:</span></span></p>
                            <table width="665" cellspacing="0" cellpadding="2">
                                <colgroup>
                                    <col width="44" />
                                    <col width="325" />
                                    <col width="282" />
                                </colgroup>
                                <tbody>
                                    <tr valign="top">
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                                            <p align="justify">№ <span style="font-size: small;">п/п</span></p>
                                        </td>
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                                            <p align="justify"><span style="font-size: small;">Условие</span></p>
                                        </td>
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                                            <p align="justify"><span style="font-size: small;">Содержание условия</span>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr valign="top">
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                                            <p align="justify"><span style="font-size: small;">1</span></p>
                                        </td>
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                                            <p align="justify"><span style="font-size: small;">Сумма займа</span></p>
                                        </td>
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                                            <p style="margin-bottom: 0in;" align="justify"><span
                                                    style="font-size: small;">00,00 руб. (00.00 рублей 00 копеек)</span>
                                            </p>
                                            <p align="justify">&nbsp;</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="margin-bottom: 0in; line-height: 100%;" align="justify">&nbsp;</p>
                            <p style="margin-bottom: 0in; line-height: 100%;" align="justify"><span
                                    style="font-size: medium;"><span
                                        style="text-decoration: line-through;">2</span><span
                                        style="text-decoration: underline;">1</span>.Изложить п.2 в следующей
                                    редакции</span></p>
                            <table width="665" cellspacing="0" cellpadding="2">
                                <colgroup>
                                    <col width="44" />
                                    <col width="325" />
                                    <col width="282" />
                                </colgroup>
                                <tbody>
                                    <tr valign="top">
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                                            <p align="center"><span style="font-size: small;">2</span></p>
                                        </td>
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                                            <p style="margin-left: 0.04in; margin-right: 0.04in;"><span
                                                    style="font-size: small;">Срок действия договора, срок возврата
                                                    займа</span></p>
                                        </td>
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                                            <p><span style="font-size: small;">До _____день,_____мес., _______ год
                                                    (включительно)</span></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="margin-bottom: 0in; line-height: 100%;" align="justify">&nbsp;</p>
                            <p style="margin-bottom: 0in; line-height: 100%;" align="justify"><span
                                    style="font-size: medium;"><span
                                        style="text-decoration: line-through;">3</span><span
                                        style="text-decoration: underline;">2</span>. Изложить п.6 в следующей
                                    редакции:</span></p>
                            <table width="665" cellspacing="0" cellpadding="2">
                                <colgroup>
                                    <col width="44" />
                                    <col width="325" />
                                    <col width="282" />
                                </colgroup>
                                <tbody>
                                    <tr valign="top">
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="44">
                                            <p align="center"><span style="font-size: small;">6</span></p>
                                        </td>
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="325">
                                            <p style="margin-left: 0.04in; margin-right: 0.04in;"><span
                                                    style="font-size: small;">Количество, размер и периодичность (сроки)
                                                    платежей заемщика по договору или порядок определения этих
                                                    платежей</span></p>
                                        </td>
                                        <td style="border: 1px solid #000000; padding: 0in 0.02in;" width="282">
                                            <p><span style="font-size: small;">Сумма займа и процентов подлежат оплате
                                                    единовременным платежом в срок, указанный в п. 2 Индивидуальных
                                                    условий.<br />Размер платежа к моменту возврата займа 00,00 руб.
                                                    (00.00 рублей 00 копеек)</span></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table width="397" cellspacing="0" cellpadding="7">
                <tbody>
                    <tr>
                        <td style="border: none; padding: 0in;" valign="bottom" width="383" height="122">
                            <table style="height: 180px; width: 78.9867%;" width="397" cellspacing="0" cellpadding="7">
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid #000000; padding: 0in 0.08in;" valign="top"
                                            width="100%" height="98">
                                            <p style="margin-bottom: 0in;">Подписано с использованием ПЭП</p>
                                            <p style="margin-bottom: 0in;">Иванова Ивана Ивановича</p>
                                            <p style="margin-bottom: 0in;">_____"_____________________" 2020</p>
                                            <p style="margin-bottom: 0in;">Телефон ??? (если возможно)</p>
                                            <p style="margin-bottom: 0in;">СМС-код</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_006.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
