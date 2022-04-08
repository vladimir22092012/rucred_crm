<table>
    <tr style="width: 100%">
        <td style="width: 50%"><img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png" style="height: 80px">
        </td>
        <td style="width: 10%"></td>
    </tr>
</table>
<div></div>
<h3 align="center"><strong>ОБЯЗАТЕЛЬСТВО О ПОДАЧЕ ЗАЯВЛЕНИЯ В АДРЕС РАБОТОДАТЕЛЯ
        НА ПЕРЕЧИСЛЕНИЕ ЧАСТИ ЗАРАБОТНОЙ ПЛАТЫ
        В СЧЁТ ОБСЛУЖИВАНИЯ МИКРОЗАЙМА
    </strong></h3>
<div></div>
<div align="justify">Настоящим я, <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper} {$birth}</strong> года рождения, место
    рождения <strong>{$birth_place|upper}</strong>, паспорт
    серия <strong>{$passport_serial} № {$passport_number} выдан {$passport_issued} {$passport_date|date}</strong> года,
    код
    подразделения <strong>{$subdivision_code}</strong>, зарегистрированный (-ая) по адресу
    <strong>{$Regadressfull}</strong>,
    фактически проживающий (-ая) по адресу <strong>{$Faktadressfull}</strong>,ИНН: <strong>{$inn}</strong> СНИЛС:
    <strong>{$snils}</strong>телефон: <strong>{$phone_mobile}</strong> адрес электронной почты: <strong>{$email|upper}</strong>
</div>
<div align="justify">Я связи с заключением между мною и Обществом с ограниченной ответственностью Микрокредитная компания «Русское
    кредитное общество» Договора микрозайма на сумму <strong>{$amount|number_format:0:',':' '} ({$amount_to_string}
        )</strong> рублей <strong>00</strong> копеек и сроком на <strong>{$loan->max_period}</strong> месяц, с целью
    соблюдения условий
    предоставления микрозайма и исполнения своих обязательств надлежащим образом обязуюсь в срок не позднее 10 (Десяти)
    рабочих дней с даты подписания настоящего обязательства направить в адрес своего работодателя заявление о
    перечислении части причитающихся мне выплат на счёт Общества с ограниченной ответственностью Микрокредитная компания
    «Русское кредитное общество» Договора как займодавца в счёт погашения задолженности по микрозайму в соответствии с
    графиком платежей.<br>
</div>
<table style="width: 100%;" border="1">
    <tr style="width: 100%">
        <td style="width: 20%; height: 50px"><span style="color: #b3b2ab">ПОДПИСЬ</span></td>
        <td style="width: 40%"><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></td>
        <td style="width: 40%"><span style="color: #b3b2ab">ДАТА ПОДПИСАНИЯ</span></td>
    </tr>
</table>