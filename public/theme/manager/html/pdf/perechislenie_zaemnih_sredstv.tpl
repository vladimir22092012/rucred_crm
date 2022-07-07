<div><br><br><br></div>
<table>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%"><strong>Генеральному директору ООО МКК «Русское кредитное общество» Лоскутову
                А.В.</strong></td>
    </tr>
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
    <h3 align="center">ЗАЯВЛЕНИЕ</h3>
    <h4 align="center">о перечислении заемных денежных средств</h4>
</div>
<div style="width: 100%; font-size: 10px" align="justify"><span>Прошу причитающиеся мне заёмные денежные средства
        в размере  <strong>{$amount|number_format:0:',':' '}</strong> (<strong>{$amount_string}</strong>) рублей <strong>00</strong> копеек по Договору микрозайма № <strong>{$uid}</strong>,
        заключённому <strong>{$probably_start_date|date}</strong> года с Обществом с ограниченной ответственностью Микрокредитная компания «Русское кредитное общество», перечислить по следующим реквизитам:
</span><br>
</div>
<table border="1" cellpadding="5">
    <tr>
        <td style="width: 30%">Получатель:</td>
        <td style="width: 70%"><strong>{$lastname} {$firstname} {$patronymic}</strong></td>
    </tr>
    <tr>
        <td style="width: 30%">Р/с получателя</td>
        <td style="width: 70%">р/с {$requisite->number} в {$requisite->name}, БИК {$requisite->bik},
            к/с {$requisite->correspondent_acc}
        </td>
    </tr>
    <tr>
        <td style="width: 30%">Назначение платежа:</td>
        <td style="width: 70%">Оплата по договору микрозайма № <strong>{$uid}</strong> от <strong>{$date|date}</strong>
            за <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper}</strong> // ИНН <strong>{$inn}</strong>
        </td>
    </tr>

</table>
<br>
<div align="justify">Настоящее заявление составлено добровольно, никакого физического / психического воздействия на меня
    оказано не было.
    Действую в своих интересах.
</div>
<div>
    <br>
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
    <table style="color: #002688; font-style: italic; border: 0.25pt solid #002088; width: 50%"
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
            <td>ID подписания: {$code_asp->uid}</td>
        </tr>
        <tr>
            <td>Код подтверждения: {$code_asp->code}</td>
        </tr>
        <tr>
            <td>Система ЭДО: Рестарт.Онлайн</td>
        </tr>
    </table>
{/if}