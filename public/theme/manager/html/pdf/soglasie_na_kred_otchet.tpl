<table>
    <tr style="width: 100%">
        <td style="width: 50%"><img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png" style="height: 80px">
        </td>
    </tr>
</table>
<div></div>
<h3 align="left"><strong>СОГЛАСИЕ НА ПОЛУЧЕНИЕ КРЕДИТНОГО ОТЧЕТА</strong></h3>
<div></div>
<div align="justify">Я, <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper} {$birth|date_format:'%d.%m.%Y'}</strong>
    года рождения, место
    рождения <strong>{$birth_place|upper}</strong>, паспорт
    серия <strong>{$passport_serial} № {$passport_number} выдан {$passport_issued|upper} {$passport_date|date}</strong>
    года,
    код
    подразделения <strong>{$subdivision_code}</strong>, зарегистрированный (-ая) по адресу
    <strong>{$regadress->adressfull|upper}</strong>,
    фактически проживающий (-ая) по адресу <strong>{$faktadress->adressfull|upper}</strong>, ИНН: <strong>{$inn}</strong>,
    СНИЛС:
    <strong>{$snils}</strong>, телефон: <strong>{$phone_mobile}</strong>, адрес электронной почты:
    <strong>{$email|upper}</strong>
</div>
<div style="font-size: 9px" align="justify">в соответствии с Федеральным законом № 218-ФЗ «О кредитных историях» и в
    целях принятия решения о предоставлении мне
    микрозайма, заключения со мной договора микрозайма настоящим даю Обществу с ограниченной ответственностью
    Микрокредитная компания «Русское кредитное общество» (ОГРН 121700334532, ИНН9725055162, регистрационный номер в
    государственном реестре микрофинансовых организаций 2103045009730, место нахождения постоянно действующего
    исполнительного органа 117449, город Москва, улица Винокурова, дом 3, этаж/комната 1/А,
    помещение I, тел. +7 (495) 803-33-30, официальный сайт http://РуКред.рф/) своё согласие на получение из любого бюро кредитных
    историй информации / кредитных отчётов обо мне.
</div>
<div style="font-size: 9px" align="justify">Я проинформирован о том, что:<br>- настоящее согласие считается
    действительным в течение 5 (Пяти) лет со дня его оформления;<br>- в случае если в течение срока, указанного в
    предыдущем абзаце, был заключён договор микрозайма,
    настоящее согласие сохраняет силу в течение всего срока действия договора микрозайма.
</div>
<div>
    <br><br><br><br><br><br><br><br><br><br><br><br><br>
</div>
{if !isset($code_asp->code)}
    <table style="width: 100%;" border="1" cellpadding="8">
        <tr style="width: 100%">
            <td style="width: 25%; height: 30px" align="center"><span style="color: #b3b2ab">ПОДПИСЬ</span></td>
            <td style="width: 50%" align="center"><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></td>
            <td style="width: 25%" align="center"><span style="color: #b3b2ab">ДАТА ПОДПИСАНИЯ</span></td>
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
