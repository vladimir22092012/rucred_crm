<table>
    <tr style="width: 100%">
        <td style="width: 50%"><img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png" style="height: 80px">
        </td>
    </tr>
</table>
<h3 align="left"><strong>СОГЛАСИЕ НА ОБРАБОТКУ ПЕРСОНАЛЬНЫХ ДАННЫХ, РАЗРЕШЁННЫХ СУБЪЕКТОМ ПЕРСОНАЛЬНЫХ ДАННЫХ ДЛЯ
        РАСПРОСТРАНЕНИЯ</strong></h3>
<div></div>
<div align="justify">Я, <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper} {$birth}</strong>
    года рождения, место
    рождения <strong>{$birth_place|upper}</strong>, паспорт
    серия <strong>{$passport_serial} № {$passport_number} выдан {$passport_issued|upper} {$passport_date|date}</strong>
    года,
    код
    подразделения <strong>{$subdivision_code}</strong>, зарегистрированный (-ая) по адресу
    <strong>{$regadress->adressfull|upper}</strong>,
    фактически проживающий (-ая) по адресу <strong>{$faktadress->adressfull|upper}</strong>, ИНН:
    <strong>{$inn}</strong>,
    СНИЛС:
    <strong>{$snils}</strong>, телефон: <strong>{$phone_mobile}</strong>, адрес электронной почты:
    <strong>{$email|upper}</strong>
</div>
<div style="font-size: 9px" align="justify">являющийся субъектом персональных данных в соответствии со статьями 9, 10
    Федерального закона от 27 июля 2006 года № 152-ФЗ «О персональных данных» настоящим подтверждаю, что <strong>даю
        своё
        согласие</strong> Обществу с ограниченной ответственностью Микрокредитная компания «Русское кредитное общество»
    (ИНН 9725055162) на предоставление Обществу с ограниченной ответственностью <strong>{$company->name}</strong> (ИНН
    <strong>{$company->inn}</strong>) следующих моих персональных
    данных, в том числе биометрических:<br>- фамилия, имя, отчество; <br>- год, месяц, дата
    и место рождения; <br>- свидетельство о гражданстве (при необходимости); <br>- реквизиты документа, удостоверяющего
    личность;<br>- идентификационный номер налогоплательщика, дата постановки его на учёт, реквизиты свидетельства
    постановки на учёт в налоговом органе; <br>- номер свидетельства обязательного пенсионного страхования, дата
    регистрации в системе
    обязательного пенсионного
    страхования; <br>- номер полиса обязательного медицинского страхования; <br>- адрес фактического места проживания и
    регистрации по месту жительства и (или) по месту пребывания; <br>- почтовый и электронный адреса; <br>- номера
    телефонов; <br>- фотографии; <br>- сведения об образовании, профессии, специальности и квалификации, реквизиты
    документов об образовании; <br>- сведения о семейном положении и составе семьи; <br>- сведения об имущественном
    положении, доходах, задолженности; <br>- сведения о занимаемых ранее должностях и стаже работы, воинской
    обязанности, воинском учёте; <br>- копии вышеперечисленных и иных документов.
</div>
<div>

</div>
{if !isset($code_asp->code)}
    <table style="width: 100%; page-break-after: always;" border="1" cellpadding="8">
        <tr style="width: 100%">
            <td style="width: 25%; height: 30px" align="center"><span style="color: #b3b2ab">ПОДПИСЬ</span></td>
            <td style="width: 50%" align="center"><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></td>
            <td style="width: 25%" align="center"><span style="color: #b3b2ab">ДАТА ПОДПИСАНИЯ</span></td>
        </tr>
    </table>
{else}
    <table style="border: 0.25pt solid #002088; font-size: 8px; width: 50%; page-break-after: always;"
           cellpadding="1" cellspacing="3">
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

<div style="font-size: 9px"><u>Цель обработки персональных данных:</u><br>- обеспечение соблюдения требований
    законодательства Российской Федерации;<br>- заключение Договора микрозайма с ООО МКК «Русское кредитное общество» и
    последующего его исполнения<br>
    <div align="justify">Обработка вышеуказанных персональных данных будет осуществляться путём смешанной
        (автоматизированной, не
        автоматизированной) обработки персональных данных.<br>Настоящее согласие на обработку персональных данных
        действует с момента его представления оператору в течение 5
        (Пяти) лет с момента предоставления и может быть отозвано мной в любое время путём подачи оператору заявления в
        простой письменной форме.<br>Персональные данные субъекта подлежат хранению в течение сроков, установленных
        законодательством Российской
        Федерации. Персональные данные уничтожаются: по достижению целей обработки персональных данных; при ликвидации
        или
        реорганизации оператора; на основании письменного обращения субъекта персональных данных с требованием о
        прекращении
        обработки его персональных данных (оператор прекратит обработку таких персональных данных в течение 3 (Трёх)
        рабочих
        дней, о чем будет направлено письменное уведомление субъекту персональных данных в течение 10 (Десяти) рабочих
        дней.
    </div>
</div>
<div>
    <br><br><br><br><br><br><br><br><br>
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
           cellpadding="1" cellspacing="3">
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