<div><strong>Приложение №1</strong><br>к Общим условиям договора микрозайма: формам и стандартам предоставления,
    использования и возврата потребительских
    микрозаймов Общества с ограниченной ответственностью Микрокредитная компания «Русское кредитное общество»
</div>
<hr>
<div></div>
<table border="1">
    <tr style="width: 100%;">
        <td style="width: 28%;" height="140">
            <img src="{$config->root_url}/theme/manager/html/pdf/i/qrcode.jpg" width="140">
        </td>
        <td style="width: 36%" align="center">
            <div>Полная стоимость микрозайма в процентах годовых
                <strong>{$percents}%<br>
                    ({$percents_per_year|upper} {if $second_part_percents|upper} ЦЕЛЫХ И {$second_part_percents|upper} ТЫСЯЧНЫХ ПРОЦЕНТОВ{/if}
                    )</strong> годовых
            </div>
        </td>
        <td style="width: 36%;" align="center">
            <div>Полная стоимость микрозайма в валюте микрозайма
                <strong>{$psk_rub} ({$amount_to_string_1|upper})</strong> рублей
                <strong>{$amount_to_string_2|upper}</strong>
                копеек
            </div>
        </td>
    </tr>
</table>
<div></div>
<div><h3>ГРАФИК ПЛАТЕЖЕЙ</h3></div>
<hr>
<div></div>
<div>ФИО: <strong>{$lastname} {$firstname} {$patronymic}</strong><br><br>паспорт гражданина РФ серия:
    <strong>{$passport_serial}</strong>, № <strong>{$passport_number}</strong><br><br>выдан:
    <strong>{$passport_date|date}</strong> года <strong>{$passport_issued}</strong>, код подразделения
    <strong>{$subdivision_code}</strong>
</div>
<div></div>
<table border="1" style="width: 100%; font-size: 8px;" cellpadding="4">

    <tr style="width: 100%;">
        <td rowspan="3" style="background-color: #b3b2ab">Дата платежа</td>
        <td colspan="4" style="background-color: #b3b2ab">Платёж за расчётный период, руб.</td>
        <td rowspan="3" style="background-color: #b3b2ab">Остаток задолженности по микрозайму, руб.</td>
    </tr>
    <tr style="width: 100%;">
        <td rowspan="2" style="background-color: #b3b2ab">Сумма платежа</td>
        <td colspan="3" style="background-color: #b3b2ab">Структура платежа</td>
    </tr>
    <tr style="width: 100%;">
        <td style="background-color: #b3b2ab">Погашение процентов</td>
        <td style="background-color: #b3b2ab">Погашение основного долга</td>
        <td style="background-color: #b3b2ab">Комиссии и другие платежи</td>
    </tr>
    {foreach $payment_schedule as $date => $payment}
        {if $date != 'result'}
            <tr>
                <td align="center">{$date}</td>
                <td align="center">{$payment['pay_sum']|floatval|number_format:2:',':' '}</td>
                <td align="center">{$payment['loan_percents_pay']|floatval|number_format:2:',':' '}</td>
                <td align="center">{$payment['loan_body_pay']|floatval|number_format:2:',':' '}</td>
                <td align="center">{$payment['comission_pay']|floatval|number_format:2:',':' '}</td>
                <td align="center">{$payment['rest_pay']|floatval|number_format:2:',':' '}</td>
            </tr>
        {/if}
    {/foreach}
    <tr>
        <td style="background-color: #b3b2ab">ИТОГО:</td>
        <td style="background-color: #b3b2ab">{$payment_schedule['result']['all_sum_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab">{$payment_schedule['result']['all_loan_percents_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab">{$payment_schedule['result']['all_loan_body_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab">{$payment_schedule['result']['all_comission_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab">{$payment_schedule['result']['all_rest_pay_sum']|floatval|number_format:2:',':' '}</td>
    </tr>
    <tr>
        <td colspan="5">Полная стоимость микрозайма, % годовых:</td>
        <td>{$percents}%</td>
    </tr>
</table>
<div style="page-break-after: always;"></div>
<h3><strong>РЕКВИЗИТЫ И ПОДПИСИ СТОРОН</strong></h3><br>
<hr style="width: 100%; size: 5px">
<div>

</div>
<table>
    <tr>
        <td style="width: 45%">ЗАИМОДАВЕЦ</td>
        <td style="width: 10%"></td>
        <td style="width: 45%">ЗАЁМЩИК</td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%"><strong>Общество с ограниченной ответственностью Микрокредитная компания «Русское
                кредитное общество»</strong></td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><strong>ФИО: {$lastname|upper} {$firstname|upper} {$patronymic|upper}</strong></td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%"><u>юридический адрес</u>
            город Москва, улица Ленинская Слобода, дом 26, строение 28, помещение I, комната 290
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>паспорт гражданина Российской Федерации</u>
            {$passport_serial} {$passport_number} выдан {$passport_issued} {$passport_date|date} года
        </td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%"><u>фактический адрес местонахождения</u>
            город Москва, улица Ленинская Слобода, дом 26, строение 28, БЦ «Слободской», офис 344
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>адрес регистрации</u><br>{$regaddress->adressfull}
        </td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%">ОГРН 121770033532</td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>фактический адрес проживания</u><br>{$faktaddress->adressfull}
        </td>
    </tr>
</table>
<div></div>
<table>
    <tr>
        <td style="width: 45%">ИНН 9725055162</td>
        <td style="width: 10%"></td>
        <td style="width: 45%"></td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%">КПП 772501001</td>
        <td style="width: 10%"></td>
        <td style="width: 45%"></td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%">р/с {$settlement->payment} в {$settlement->name}, БИК {$settlement->bik},
            к/с {$settlement->cors} в ГУ
            Банка России по ЦФО
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"></td>
    </tr>
    <br>
    <br>
    <tr>
        <td style="width: 45%">__________________/<strong>А.В.Лоскутов</strong>/</td>
        <td style="width: 10%"></td>
        {if !isset($sms)}
            <td style="width: 45%">
                __________________/<strong>{$firstname|mb_substr:0:1}.{$patronymic|mb_substr:0:1}.{$lastname}/</strong>
            </td>
        {else}
            <td style="width: 45%"></td>
        {/if}
    </tr>
    <br>
</table>
<div>
    <br>
</div>
{if isset($sms)}
    <table style="color: #002688; page-break-after: always; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="5">
        <tr>
            <td>ДОКУМЕНТ ПОДПИСАН ЭЛЕКТРОННОЙ ПОДПИСЬЮ</td>
        </tr>
        <tr>
            <td>Подписант: {$firstname} {$patronymic} {$lastname}</td>
        </tr>
        <tr>
            <td>Дата подписания: {$confirm_date|date} {$confirm_date|time}(МСК)</td>
        </tr>
        <tr>
            <td>ID подписания: {$code_asp->id}</td>
        </tr>
        <tr>
            <td>Код подтверждения: {$code_asp->code}</td>
        </tr>
        <tr>
            <td>Система ЭДО: Рестарт.Онлайн</td>
        </tr>
    </table>
{/if}