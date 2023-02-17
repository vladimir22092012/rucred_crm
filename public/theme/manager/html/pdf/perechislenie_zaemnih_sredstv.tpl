<div><br><br><br></div>
<table>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%"><strong>Генеральному директору ООО МКК «Русское кредитное общество» Лоскутову
                А.В.</strong></td>
    </tr>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%">от заемщика <strong>{$lastname} {$firstname} {$patronymic}</strong></td>
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
        <td style="width: 40%">зарегистрированного по адресу: <strong>{$regadress->adressfull}</strong></td>
    </tr>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%">фактически проживающего: <strong>{$faktadress->adressfull}</strong></td>
    </tr>
</table>
<div><br><br></div>
<div style="width: 100%">
    <h3 align="center">ЗАЯВЛЕНИЕ</h3>
    <h4 align="center">о перечислении заемных денежных средств</h4>
</div>
<div style="width: 100%; font-size: 10px" align="justify"><span>Прошу причитающиеся мне заёмные денежные средства
        в размере  <strong>{$amount|number_format:0:',':' '}</strong> (<strong>{$amount_string}</strong>) рублей <strong>00</strong> копеек по Договору микрозайма № <strong>{$uid}</strong>,
        заключённому <strong>{if isset($code_asp->code)}{$code_asp->created|date}{else}ХХ.ХХ.ХХХХ{/if}</strong> года с Обществом с ограниченной ответственностью Микрокредитная компания «Русское кредитное общество», перечислить по следующим реквизитам:
</span><br>
</div>
<table border="1" cellpadding="5">
    <tr>
        <td style="width: 30%">Получатель:</td>
        <td style="width: 70%"><strong>{$requisite->holder|upper}</strong></td>
    </tr>
    <tr>
        <td style="width: 30%">Р/с получателя</td>
        <td style="width: 70%">р/с {$requisite->number} в {$requisite->name}, БИК {$requisite->bik},
            к/с {$requisite->correspondent_acc}
        </td>
    </tr>
    <tr>
        <td style="width: 30%">Назначение платежа:</td>
        <td style="width: 70%">Выдача средств по договору микрозайма № <strong>{$uid}</strong> от <strong>{if isset($code_asp->code)}{$code_asp->created|date}{else}ХХ.ХХ.ХХХХ{/if}</strong>
            // заемщик <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper}</strong> ИНН <strong>{$inn}</strong>
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
