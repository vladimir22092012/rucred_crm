<div><br><br><br></div>
<table>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%"><strong>Генеральному директору ООО МКК «Русское кредитное
                общество» Лоскутову А.В.</strong></td>
    </tr>
    <br>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%">от <strong>{$lastname} {$firstname} {$patronymic}</strong></td>
    </tr>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%">паспорт серии <strong>{$passport_serial}</strong> номер
            <strong>{$passport_number}</strong></td>
    </tr>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%">ИНН <strong>{$inn}</strong></td>
    </tr>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%">зарегистрированный по адресу: <strong>{$regadress->adressfull}</strong></td>
    </tr>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%">фактически проживающий: <strong>{$faktadress->adressfull}</strong></td>
    </tr>
</table>
<div><br><br></div>
<div style="width: 100%">
    <h3 align="left">ЗАЯВЛЕНИЕ<br>о реструктуризации задолженности по договору микрозайма<br>№ {$uid}
        от {$probably_start_date|date} 2022 года
    </h3>
</div>
<div style="width: 100%; font-size: 10px" align="justify">Настоящим прошу рассмотреть возможность и осуществить
    реструктуризацию моих обязательств по Договору микрозайма № <strong>{$uid}</strong>, заключённому
    <strong>{$probably_start_date|date}</strong> года с Обществом с
    ограниченной ответственностью Микрокредитная компания «Русское кредитное общество», на следующих условиях:
    <br>
</div>
<table border="1" cellpadding="5">
    <tr>
        <td style="width: 30%">Дата реструктуризации:</td>
        <td style="width: 70%"><strong>{$restruct_date|date}</strong></td>
    </tr>
    <tr>
        <td style="width: 30%">Сумма платежа на дату реструктуризации:</td>
        <td style="width: 70%"><strong>{$annouitet|floatval|number_format:2:',':' '}({$annouitet_first_part|upper}
                )</strong> рублей <strong>{$annouitet_second_part}</strong> {$annouitet_second_part|plural:'копейка':'копеек': 'копейки'}</td>
    </tr>
    <tr>
        <td style="width: 30%">Сумма процентов к погашению:</td>
        <td style="width: 70%"><strong>{$payment_schedule['result']['all_loan_percents_pay']|floatval|number_format:2:',':' '}</strong>(<strong>{$all_percents_string_part_one|upper}</strong>)
            рублей
            <strong>{if $all_percents_string_part_two}{$all_percents_string_part_two}{else}00{/if}</strong>
            {$all_percents_string_part_two|plural:'копейка':'копеек': 'копейки'}<br></td>
    </tr>
    <tr>
        <td style="width: 30%">Сумма основного долга к погашению:</td>
        <td style="width: 70%"><strong>{$payment_schedule['result']['all_loan_body_pay']|floatval|number_format:2:',':' '}</strong><strong>({$amount_string|upper}
                )</strong> рублей
            <strong>00</strong> копеек
        </td>
    </tr>
</table>
<div style="width: 100%; font-size: 10px" align="justify"><br>Прошу также произвести:<br>☐ отсрочку по уплате
    непогашенных процентов в сумме [●] ([●]) руб. на 1 (Один) месяц;<br>☐ без увеличения срока микрозайма;<br>☐
    увеличение срока микрозайма на [●] ([●]) месяцев.
</div>
<div style="width: 100%; font-size: 10px" align="justify">Надлежащее обслуживание микрозайма после реструктуризации
    гарантирую.<br><br>После удовлетворения моего заявления на реструктуризацию моих обязательств по Договору микрозайма
    от <strong>{$probably_start_date|date}</strong>
    года № <strong>{$uid}</strong> прошу в соответствии с условиями указанного выше договора микрозайма произвести подготовить
    дополнительное соглашение к указанному выше договору микрозайма и осуществить перерасчёт графика его обслуживания.
</div>
<div style="width: 100%; font-size: 10px" align="justify">Настоящее заявление составлено добровольно, никакого
    физического / психического воздействия на меня оказано не было. Действую в своих интересах.
</div>
{if !isset($code_asp->code)}
    <table>
        <tr style="width: 100%">
            <td style="width: 30%"><strong>{$date|date} года</strong></td>
            <td style="width: 20%"></td>
            <td style="width: 40%">_______________/____________</td>
        </tr>
    </table>
{else}
    <table style="border: 0.25pt solid #002088; font-size: 8px; width: 50%"
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
{/if}