<div><strong>Приложение №1</strong><br>к Общим условиям договора микрозайма: формам и стандартам предоставления,
    использования и возврата потребительских
    микрозаймов Общества с ограниченной ответственностью Микрокредитная компания «Русское кредитное общество» к Договору микрозайма
    от <strong>{$created_date|date}</strong> года № <strong>{$uid}</strong>
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
                <strong>{$payment_schedule['result']['all_loan_percents_pay']|number_format:0:',':' '}</strong>
                (<strong>{$all_percents_string_part_one|upper}</strong>)
                рублей
                <strong>{if $all_percents_string_part_two}{$all_percents_string_part_two}{else}00{/if}</strong>
                {$all_percents_string_part_two|plural:'копейка':'копеек':'копейки'}
            </div>
        </td>
    </tr>
</table>
<div><h3>ГРАФИК ПЛАТЕЖЕЙ</h3></div>
<hr>
<div></div>
<div>ФИО: <strong>{$lastname} {$firstname} {$patronymic}</strong><br><br>паспорт гражданина РФ серия:
    <strong>{$passport_serial}</strong>, № <strong>{$passport_number}</strong><br><br>выдан:
    <strong>{$passport_date|date}</strong> года <strong>{$passport_issued}</strong>, код подразделения
    <strong>{$subdivision_code}</strong>
</div>
<div></div>
<table border="1" style="width: 100%; font-size: 8px;" cellpadding="3">
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
        <td style="background-color: #b3b2ab" align="center">ИТОГО:</td>
        <td style="background-color: #b3b2ab" align="center">{$payment_schedule['result']['all_sum_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab" align="center">{$payment_schedule['result']['all_loan_percents_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab" align="center">{$payment_schedule['result']['all_loan_body_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab" align="center">{$payment_schedule['result']['all_comission_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab" align="center">{$payment_schedule['result']['all_rest_pay_sum']|floatval|number_format:2:',':' '}</td>
    </tr>
    <tr>
        <td colspan="5">Полная стоимость микрозайма, % годовых:</td>
        <td>{$percents}%</td>
    </tr>
</table>
<div>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; font-size: 8px; page-break-after: always;" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 1</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="border: 0.25pt solid #002088; font-size: 8px; width: 50%; page-break-after: always;"
           cellpadding="1" cellspacing="2">
        <tr>
            <td colspan="2"><strong>ДОКУМЕНТ ПОДПИСАН ЭЛЕКТРОННОЙ ЦИФРОВОЙ ПОДПИСЬЮ</strong></td>
        </tr>
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Подписант:</strong> {$firstname} {$patronymic} {$lastname}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Дата подписания:</strong> {$code_asp->created|date} {$code_asp->created|time}(МСК)</td>
        </tr>
        <tr>
            <td colspan="2"><strong>ID подписания:</strong> {$code_asp->uid}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Код подтверждения:</strong> {$code_asp->code}</td>
        </tr>
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td><strong>Система ЭДО:</strong> Рестарт.Онлайн</td>
            <td><img src="{$config->root_url}/theme/manager/html/pdf/i/Vector.png" style="height: 27px"></td>
        </tr>
    </table>
{/if}
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
            117449, город Москва, вн.тер.г.муниципальный округ Академический, улица Винокурова, дом 3, этаж/ком. 1/А,
            пом./ком. I/1-3,6-11
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>паспорт гражданина Российской Федерации</u>
            {$passport_serial} {$passport_number} выдан {$passport_issued} {$passport_date|date} года
        </td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%"><u>фактический адрес местонахождения</u>
            117449, город Москва, улица Винокурова, дом 3, этаж/комната 1/А,
            помещение I
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>адрес регистрации</u><br>{$regadress->adressfull}
        </td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%">ОГРН 121770033532</td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>фактический адрес проживания</u><br>{$faktadress->adressfull}
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
        <td style="width: 45%">КПП 772701001</td>
        <td style="width: 10%"></td>
        <td style="width: 45%"></td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%">р/с {$settlement->payment} в {$settlement->name}, БИК {$settlement->bik},
            к/с {$settlement->cors}
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"></td>
    </tr>
    <br>
    <br>
    <tr>
        {if !isset($code_asp->code)}
            <td style="width: 45%">
                __________________/<strong>А.В.Лоскутов/</strong>
            </td>
            <td style="width: 10%">
            </td>
            <td style="width: 45%">
                __________________/<strong>{$firstname|mb_substr:0:1}.{$patronymic|mb_substr:0:1}.{$lastname}/</strong>
            </td>
        {else}
            <td style="width: 45%">
                <table style="border: 0.25pt solid #02a900; font-size: 8px"
                       cellpadding="1" cellspacing="4">
                    <tr>
                        <td colspan="2"><strong>ДОКУМЕНТ ПОДПИСАН ЭЛЕКТРОННОЙ ЦИФРОВОЙ ПОДПИСЬЮ</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Подписант:</strong> Генеральный директор<br>ООО МКК "Русское кредитное общество"<br>А.В. Лоскутов</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Дата подписания:</strong> {$rucred_asp->created|date} {$rucred_asp->created|time}(МСК)</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>ID подписания:</strong> {$rucred_asp->uid}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr></td>
                    </tr>
                    <tr>
                        <td><strong>Система ЭДО:</strong> Рестарт.Онлайн</td>
                        <td><img src="{$config->root_url}/theme/manager/html/pdf/i/Mask_group.png" style="height: 22px"></td>
                    </tr>
                </table>
            </td>
            <td style="width: 10%">
            </td>
            <td style="width: 45%">
                <table style="border: 0.25pt solid #002088; font-size: 8px"
                       cellpadding="1" cellspacing="6">
                    <tr>
                        <td colspan="2"><strong>ДОКУМЕНТ ПОДПИСАН ЭЛЕКТРОННОЙ ЦИФРОВОЙ ПОДПИСЬЮ</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Подписант:</strong> {$firstname} {$patronymic} {$lastname}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Дата подписания:</strong> {$code_asp->created|date} {$code_asp->created|time}(МСК)</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>ID подписания:</strong> {$code_asp->uid}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Код подтверждения:</strong> {$code_asp->code}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr></td>
                    </tr>
                    <tr>
                        <td><strong>Система ЭДО:</strong> Рестарт.Онлайн</td>
                        <td><img src="{$config->root_url}/theme/manager/html/pdf/i/Vector.png" style="height: 27px"></td>
                    </tr>
                </table>
            </td>
        {/if}
    </tr>
</table>