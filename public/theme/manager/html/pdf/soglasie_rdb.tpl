<table>
    <tr style="width: 100%">
        <td style="width: 50%"><img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png" style="height: 80px">
        </td>
    </tr>
</table>
<div></div>
<h3 align="left"><strong>СОГЛАСИЕ НА ОБРАБОТКУ ПЕРСОНАЛЬНЫХ ДАННЫХ</strong></h3>
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
<div align="justify">в соответствии с Федеральным законом от 27.07.2006 года № 152-ФЗ «О персональных данных» для
    реализации следующих
    целей: <br>(01) заключения и исполнения договоров микрозайма;<br>(02) предоставления скидок и льготных условий по
    договорам микрозайма;<br>(03) получения предложений продуктов и услуг Общества;<br>(04) получения предложений
    продуктов и услуг контрагентов Общества, приёма на обслуживание в порядке,
    предусмотренном Правилами предоставления и обслуживания микрозаймов Обществом<br><br><strong>принимаю решение и даю
        согласие</strong> на обработку, любое действие (операцию) или совокупность действий
    (операций), совершаемых с использованием средств автоматизации или без использования таких средств,
    включая сбор, запись, систематизацию, накопление, хранение, уточнение (обновление, изменение), извлечение,
    использование, передачу (предоставление, доступ),
    обезличивание, блокирование, удаление, уничтожение) <strong>Обществу с ограниченной ответственностью Микрокредитная
        компания
        «Русское кредитное общество»</strong>
    (ОГРН 121700334532, ИНН9725055162, регистрационный номер в государственном реестре микрофинансовых организаций
    2103045009730,
    место нахождения постоянно действующего исполнительного органа 117449, город Москва, улица Винокурова, дом 3, этаж/комната 1/А,
    помещение I, тел. +7 (495) 803-33-30, официальный сайт http://РуКред.рф/), ранее и далее именуемое Общество, с
    привлечением уполномоченного банка <strong>ПАО «РосДорБанк»</strong> (лицензия № 1573 от «26» января 2018 года, ИНН
    7718011918, КПП
    772701001, БИК 044525666, юридический адрес 115093, город Москва, улица Дубининская, дом 86) на проведение процедуры
    упрощённой идентификации в соответствии с требованиями Федерального закона от 27.06.2011 г. № 161-ФЗ «О национальной
    платёжной системе» и Федерального закона от 07.08.2001 № 115-ФЗ «О противодействии легализации (отмыванию) доходов,
    полученных преступным путём, и финансированию терроризма».<br>Данное согласие действует до достижения целей
    обработки персональных данных или в течение срока хранения информации.<br><br>Данное согласие может быть отозвано в
    любой момент по моему письменному заявлению.<br>Я подтверждаю, что, давая
    такое согласие, я действую по собственной воле и в своих интересах.
</div>
<div align="justify">Банк <strong>ПАО «РосДорБанк»</strong> является кредитной организацией, соответствующей требованиям
    Указания Банка
    России от
    11.12.2019 N 5351-У «О требованиях к кредитным организациям, которым может быть поручено проведение идентификации
    или упрощённой идентификации, а также к микрофинансовым организациям, которые могут поручать кредитным организациям
    проведение идентификации или упрощённой идентификации».
</div>
<div>
    <br>
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
    <table style="border: 0.25pt solid #002088; width: 50%; font-size: 8px"
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
            <td><img src="{$config->root_url}/theme/manager/html/pdf/i/Vector.png" style="height: 25px"></td>
        </tr>
    </table>
{/if}
