<table>
    <tr style="width: 100%">
        <td style="width: 50%"><img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png" style="height: 80px">
        </td>
        <td style="width: 10%"></td>
    </tr>
</table>
<div></div>
<h3 align="left"><strong>ОБЯЗАТЕЛЬСТВО О ПОДАЧЕ ЗАЯВЛЕНИЯ В АДРЕС РАБОТОДАТЕЛЯ
        НА ПЕРЕЧИСЛЕНИЕ ЧАСТИ ЗАРАБОТНОЙ ПЛАТЫ
        В СЧЁТ ОБСЛУЖИВАНИЯ МИКРОЗАЙМА
    </strong></h3>
<div></div>
<div align="justify">Настоящим я, <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper} {$birth}</strong>
    года рождения, место
    рождения <strong>{$birth_place|upper}</strong>, паспорт
    серия <strong>{$passport_serial} № {$passport_number} выдан {$passport_issued} {$passport_date|date}</strong> года,
    код
    подразделения <strong>{$subdivision_code}</strong>, зарегистрированный (-ая) по адресу
    <strong>{$regadress->adressfull}</strong>,
    фактически проживающий (-ая) по адресу <strong>{$faktadress->adressfull}</strong>,ИНН: <strong>{$inn}</strong>
    СНИЛС:
    <strong>{$snils}</strong>телефон: <strong>{$phone_mobile}</strong> адрес электронной почты:
    <strong>{$email|upper}</strong>
</div>
<div align="justify">Я связи с заключением между мною и Обществом с ограниченной ответственностью Микрокредитная
    компания «Русское
    кредитное общество» Договора микрозайма от <strong>{$probably_start_date|date}</strong> года №
    <strong>{$uid}</strong> на сумму <strong>{$amount|number_format:0:',':' '} ({$amount_string|upper})</strong> рублей <strong>00</strong> копеек и сроком на
    <strong>{$loan->max_period}</strong> {$loan->max_period|plural:'месяц':'месяцев':'месяца'}, с целью
    соблюдения условий
    предоставления микрозайма и исполнения своих обязательств надлежащим образом обязуюсь в срок не позднее 10 (Десяти)
    рабочих дней с даты подписания настоящего обязательства направить в адрес своего работодателя заявление о
    перечислении части причитающихся мне выплат на счёт Общества с ограниченной ответственностью Микрокредитная компания
    «Русское кредитное общество» Договора как займодавца в счёт погашения задолженности по микрозайму в соответствии с
    графиком платежей.
</div>
<div align="justify">Я проинформирован о том, что подача указанного заявления работодателю о перечислении части
    причитающейся мне
    заработной платы является обязательным и существенным условием получения и пользования микрозаймом, невыполнение
    которого может привести к требованию досрочного погашения предоставленного микрозайма.
</div>
<div>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
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
