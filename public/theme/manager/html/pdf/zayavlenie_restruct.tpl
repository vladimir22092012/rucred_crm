<div><br></div>
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
<div><br></div>
<div style="width: 100%">
    <h3 align="left">ЗАЯВЛЕНИЕ<br>о реструктуризации задолженности по договору микрозайма<br>№ {$uid}
        от {$doc_created|date} 2022 года
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
        <td style="width: 70%"><strong>{$doc_created|date}</strong></td>
    </tr>
    <tr>
        <td style="width: 30%">Сумма платежа на дату реструктуризации:</td>
        <td style="width: 70%"><strong>{$pay_sum[0]} ({$pay_sum_string[0]})</strong> рублей <strong> {$pay_sum[1]} </strong> копеек </td>
    </tr>
    <tr>
        <td style="width: 30%">Сумма основного долга к погашению:</td>
        <td style="width: 70%"><strong>{$loan_body_pay[0]} ({$loan_body_pay_string[0]})</strong> рублей <strong> {$loan_body_pay[1]} </strong> копеек</td>
    </tr>
    <tr>
        <td style="width: 30%">Сумма процентов к погашению:</td>
        <td style="width: 70%"><strong>{$loan_percents_pay[0]} ({$loan_percents_pay_string[0]})</strong> рублей <strong> {$loan_percents_pay[1]} </strong> копеек </td>
    </tr>
    <tr>
        <td style="width: 30%">Сумма прочих комиссий и штрафов:</td>
        <td style="width: 70%"><strong>{$comission_pay[0]} ({$comission_pay_string[0]})</strong> рублей <strong> {$comission_pay[1]} </strong> копеек</td>
    </tr>
</table>
<div style="width: 100%; font-size: 10px" align="justify"><br>Прошу также произвести:<br>&#10065; отсрочку по уплате
    непогашенных процентов в сумме 0,00 (НОЛЬ) руб. на 1 (Один) месяц;<br>{if $term == 0}&#x2611;{else}&#10065;{/if} без увеличения срока микрозайма;<br>{if $term != 0}&#x2611;{else}&#10065;{/if}
    увеличение срока микрозайма на {$term} ({$string_term}) {$term|plural:'месяц':'месяцев':'месяца'}.
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
<div>

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
    <table style="border: 0.25pt solid #002088; font-size: 8px; width: 50%;"
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