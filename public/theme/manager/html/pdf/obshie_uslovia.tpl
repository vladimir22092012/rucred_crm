<div>
    <br>
</div>
<table>
    <tr style="width: 100%">
        <td style="width: 45%"><img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png">
        </td>
        <td style="width: 5%">

        </td>
        <td style="width: 50%" rowspan="3">
            <table border="1" cellpadding="9">
                <tr>
                    <td>
                        <span>ФИО заёмщика:</span><br>
                        <span><strong>{$lastname} {$firstname} {$patronymic}</strong></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Серия и номер паспорта:</span><br>
                        <span><strong>{$passport_serial} {$passport_number}</strong></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Номер и дата договора микрозайма:</span><br>
                        <span><strong>№ {$uid} от {$probably_start_date|date}</strong></span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="width: 45%"></td>
        <td style="width: 5%"></td>
        <td style="width: 50%"></td>
    </tr>
    <tr>
        <td style="width: 45%" align="left"><h3>ОСНОВНЫЕ УСЛОВИЯ<br>ДОГОВОРА МИКРОЗАЙМА</h3></td>
        <td style="width: 5%"></td>
        <td style="width: 50%"></td>
    </tr>
</table>
<div>

</div>
<table border="1" cellpadding="10">
    <tr>
        <td style="width: 51%"><strong>1.</strong> Сумма предоставленного микрозайма</td>
        <td style="width: 49%"><strong>{$amount|number_format:0:',':' '} ({$amount_string|upper})</strong> рублей</td>
    </tr>
    <tr>
        <td style="40%"><strong>2.</strong> Целевое назначение микрозайма</td>
        <td style="60%">{if $loan->reason_flag == 0}&#x2611;{else}&#10065;{/if} На неотложные
            нужды<br>{if $loan->reason_flag == 1}&#x2611;{else}&#10065;{/if} На рефинансирование обязательств перед
            третьими лицами
        </td>
    </tr>
    <tr>
        <td style="40%"><strong>3.</strong> Дата предоставления микрозайма</td>
        <td style="60%"><strong>{$probably_start_date|date}</strong> года</td>
    </tr>
    <tr>
        <td style="40%"><strong>4.</strong> Срок микрозайма</td>
        <td style="60%"><strong>{$period|escape}</strong>
            (<strong>{$period_str|upper}</strong>)
            {$period|plural:'день':'дней':'дня'}</td>
    </tr>
    <tr>
        <td style="40%"><strong>5.</strong> Дата погашения микрозайма</td>
        <td style="60%"><strong>{$probably_return_date|date}</strong> года</td>
    </tr>
    <tr>
        <td style="40%"><strong>6.</strong> Ставка по микрозайму</td>
        <td style="60%"><strong>{$percents}%
                ({$percents_per_year|upper} {if $second_part_percents|upper} ЦЕЛЫХ И {$second_part_percents|upper} ТЫСЯЧНЫХ ПРОЦЕНТОВ{/if})</strong> годовых
        </td>
    </tr>
    <tr>
        <td style="40%"><strong>7.</strong> График платежей по микрозайму</td>
        <td style="60%">Представлен в таблице ниже</td>
    </tr>
</table>
<div>
    <br>
</div>
<table border="1" style="width: 100%; font-size: 8px; page-break-after: always" cellpadding="4">
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
        <td style="background-color: #b3b2ab"
            align="center">{$payment_schedule['result']['all_sum_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab"
            align="center">{$payment_schedule['result']['all_loan_percents_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab"
            align="center">{$payment_schedule['result']['all_loan_body_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab"
            align="center">{$payment_schedule['result']['all_comission_pay']|floatval|number_format:2:',':' '}</td>
        <td style="background-color: #b3b2ab"
            align="center">{$payment_schedule['result']['all_rest_pay_sum']|floatval|number_format:2:',':' '}</td>
    </tr>
    <tr>
        <td colspan="5">Полная стоимость микрозайма, % годовых:</td>
        <td>{$percents}%</td>
    </tr>
</table>
<div>
    <br>
</div>
<table style="width: 100%; page-break-after: always" border="1" cellpadding="7">
    <tr>
        <td style="width: 50%"><strong>8.</strong> Варианты обслуживания микрозайма</td>
        <td style="width: 50%">(01) Через работодателя на основании заявления о перечислении части причитающихся
            выплат в счёт обслуживания микрозайма<br><br>(02) Самостоятельно путём безналичного перевода денежных
            средств:<br><br>
            {if $settlement_id == 1}&#x2611;{else}&#10065;{/if} <u>в АО «МИнБанк»</u><br><br>
            - получатель ООО МКК «Русское кредитное общество»<br>
            - р/с 40701810200070000086<br>
            - БИК: 044525600<br>
            - к/с 30101810300000000600 в ГУ Банка России по ЦФО<br>
            - назначение платежа «Оплата по договору микрозайма № <strong>{$uid} от {$probably_start_date|date}</strong>
            года»<br><br>
            {if $settlement_id == 2}&#x2611;{else}&#10065;{/if} <u>в ПАО «РосДорБанк»</u><br><br>
            - получатель ООО МКК «Русское кредитное общество»<br>
            - р/с 40701810300000000347<br>
            - БИК: 044525666<br>
            - к/с 30101810945250000666 в ГУ Банка России по ЦФО<br>
            - назначение платежа «Оплата по договору микрозайма № <strong>{$uid} от {$probably_start_date|date}</strong>
            года»<br><br><strong>ВНИМАНИЕ!</strong> При обслуживании путём осуществления самостоятельного безналичного
            перевода
            денежных средств
            банком-отправителем может удерживаться дополнительная комиссия. Размер комиссии определяется в
            соответствии с тарифами банка
        </td>
    </tr>
    <tr>
        <td style="width: 50%"><strong>9.</strong> Целевое назначение микрозайма</td>
        <td style="width: 50%">(01) По умолчанию обслуживание микрозайма осуществляется через работодателя<br><br>(02)
            При невыплате по микрозайму работодателем (не по причине изменения графика выплат по положениям о
            трудовом распорядке) в течение 7 дней возникает обязанность произвести текущее обслуживание по микрозайму
            самостоятельно
        </td>
    </tr>
    <tr>
        <td style="width: 50%"><strong>10.</strong> Дата предоставления микрозайма</td>
        <td style="width: 50%">Допускается досрочное полное или частичное погашение микрозайма<br><br>- предварительное
            уведомление не требуется<br>- дополнительные комиссии и/или штрафы не взимаются<br><br>При частичном
            досрочном
            погашении суммы выплат по микрозайму будут пересчитаны автоматически
        </td>
    </tr>
</table>
<div>

</div>
<table style="width: 100%;" border="1" cellpadding="7">
    <tr>
        <td style="width: 50%"><strong>11.</strong> Дата предоставления микрозайма</td>
        <td style="width: 50%">(01) При нарушении графика обслуживания и/или объёмов возможно взыскание неустойки из
            расчёта 0,05% за каждый день просрочки, начиная с восьмого дня, от непогашенной суммы долга, но не более 15%
            годовых<br><br>(02) При нецелевом использовании микрозайма возможно досрочное истребование суммы долга с
            процентами за срок
            использования за счёт зарплатных и/или премиальных выплат
        </td>
    </tr>
    <tr>
        <td style="width: 50%"><strong>12.</strong> Дата предоставления микрозайма</td>
        <td style="width: 50%">(01) При изменении графика выплат заработной платы и/или премиальных выплат
            работодателем, из которых идёт обслуживание микрозайма, штрафы и пени не применяются и не
            начисляются<br><br>(02) Пени и штрафы при нарушении условий обслуживания микрозайма в срок до 7 дней не
            начисляются
        </td>
    </tr>
</table>