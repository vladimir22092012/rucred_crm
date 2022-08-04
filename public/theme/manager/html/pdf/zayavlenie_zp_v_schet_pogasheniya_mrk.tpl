<div><br><br><br></div>
<table>
    <tr style="width: 100%">
        <td style="width: 60%"></td>
        <td style="width: 40%"><strong>Руководителю {$company->name}</strong></td>
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
    <h4 align="center">о перечислении части заработной платы на счёт третьего лица</h4>
</div>
<div style="width: 100%; font-size: 10px" align="justify"><span>Прошу Вас перечислять часть причитающихся мне выплат в счёт платежей по основному долгу и процентам за пользование денежными средствами по Договору микрозайма <strong> № {$uid}</strong>,
    заключённому между мною и Обществом с ограниченной ответственностью Микрокредитная компания «Русское кредитное общество» (ОГРН 121770033532, ИНН 9725055162), в размере соответствующем платежному периоду, указанному в Графике платежей, являющимся неотъемлемой частью указанного Договора, на счёт ООО МКК «Русское кредитное общество» по нижеуказанным реквизитам:
</span><br>
</div>
<table border="1" cellpadding="5">
    <tr>
        <td style="width: 30%">Получатель:</td>
        <td style="width: 70%">ООО МКК «Русское кредитное общество»</td>
    </tr>
    <tr>
        <td style="width: 30%">ИНН получателя:</td>
        <td style="width: 70%">9725055162</td>
    </tr>
    <tr>
        <td style="width: 30%">КПП получателя:</td>
        <td style="width: 70%">772701001</td>
    </tr>
    <tr>
        <td style="width: 30%">Р/с получателя</td>
        <td style="width: 70%">р/с {$settlement->payment} в {$settlement->name}, БИК {$settlement->bik},
            к/с {$settlement->cors}
        </td>
    </tr>
    <tr>
        <td style="width: 30%">Назначение платежа:</td>
        <td style="width: 70%">Выдача средств по договору микрозайма № <strong>{$uid}</strong> от <strong>{$date|date}</strong>
            // заемщик <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper}</strong> ИНН <strong>{$inn}</strong>
        </td>
    </tr>

</table>
<div align="justify">
    <br>В случае расторжения со мной Трудового договора, остаток задолженности по Договору микрозайма прошу перечислить
    в
    полном объёме из причитающихся мне при увольнении выплат и компенсаций по вышеуказанным реквизитам.
</div>
<div>Приложение:<br>Копия Графика платежей от <strong>{$date|date}</strong> года<br>
</div>
<div><br><br></div>
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