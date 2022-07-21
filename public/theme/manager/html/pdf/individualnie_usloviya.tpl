<br><br><br><br>
<table>
    <tr style="width: 100%">
        <td style="width: 50%"><img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png" style="height: 80px">
        </td>
    </tr>
    <tr style="width: 100%">
        <br>
        <td style="width: 100%" align="left"><strong>Общество с ограниченной ответственностью</strong></td>
    </tr>
    <tr style="width: 100%">
        <td style="width: 100%" align="left"><strong>Микрокредитная компания «Русское кредитное общество»</strong></td>
    </tr>
    <tr style="width: 100%">
        <br>
        <td style="width: 100%; font-size: 8px;" align="justify">юридический адрес
            117449, город Москва, вн.тер.г.муниципальный округ Академический, улица Винокурова, дом 3, этаж/ком. 1/А,
            пом./ком.
            I/1-3,6-11, фактический адрес места нахождения 117449, город Москва, улица Винокурова, дом 3, этаж/комната
            1/А,
            помещение I, внесённое в Единый государственный реестр МИФНС России №25 по гор. Москве 17 июля 2021 года за
            основным
            государственным регистрационным номером 121770033532, ИНН 9725055162, КПП 772701001, зарегистрированное в
            Саморегулируемой организации «Микрофинансирование и Развитие» (СРО «МиР», ОГРН 1137799014055) за №77001218
            от
            29.09.2021 года
        </td>
    </tr>
</table>
<div></div>
<table border="1">
    <tr style="width: 100%;">
        <td style="width: 28%;" height="138">
            <img src="{$config->root_url}/theme/manager/html/pdf/i/qrcode.jpg" width="138">
        </td>
        <td style="width: 36%" align="center">
            <div>Полная стоимость микрозайма в процентах годовых
                <strong>{$percents}%<br>
                    ({$percents_per_year|upper} {if $second_part_percents|upper} ЦЕЛЫХ И {$second_part_percents|upper} ТЫСЯЧНЫХ ПРОЦЕНТОВ{/if})</strong> годовых
            </div>
        </td>
        <td style="width: 36%;" align="center">
            <div>Полная стоимость микрозайма в валюте микрозайма
                <strong>{$payment_schedule['result']['all_loan_percents_pay']|number_format:2:',':' '}</strong>
                (<strong>{$all_percents_string_part_one|upper}</strong>)
                рублей
                <strong>{if $all_percents_string_part_two}{$all_percents_string_part_two}{else}00{/if}</strong>
                {$all_percents_string_part_two|plural:'копейка':'копеек':'копейки'}
            </div>
        </td>
    </tr>
</table>
<div><h3>ДОГОВОР МИКРОЗАЙМА № {$uid}</h3></div>
<table>
    <tr>
        <td style="20%" align="left">город Москва</td>
        <td style="70%"></td>
        <td style="10%" align="right">{$date|date} года</td>
    </tr>
</table>
<div align="justify"><br>Общество с ограниченной ответственностью Микрокредитная компания «Русское кредитное общество»,
    юридический адрес
    117449, город Москва, вн.тер.г.муниципальный округ Академический, улица Винокурова, дом 3, этаж/ком. 1/А, пом./ком.
    I/1-3,6-11, фактический адрес места нахождения 117449, город Москва, улица Винокурова, дом 3, этаж/комната 1/А,
    помещение I, внесённое в Единый государственный реестр МИФНС России №25 по гор. Москве 17 июля 2021 года за основным
    государственным регистрационным номером 121770033532, ИНН 9725055162, КПП 772701001, зарегистрированное в
    Саморегулируемой организации «Микрофинансирование и Развитие» (СРО «МиР», ОГРН 1137799014055) за №77001218 от
    29.09.2021 года, именуемое в
    дальнейшем «Заимодавец», в лице Генерального директора Лоскутова Алексея Викторовича, действующего на основании
    Устава, с одной стороны и гражданин (-ка) Российской
    Федерации <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper} {$birth}</strong> года рождения, место
    рождения <strong>{$birth_place|upper}</strong>, паспорт
    серия <strong>{$passport_serial} № {$passport_number} выдан {$passport_issued} {$passport_date|date}</strong> года,
    код
    подразделения <strong>{$subdivision_code}</strong>, зарегистрированный (-ая) по адресу
    <strong>{$regadress->adressfull|upper}</strong>,
    фактически проживающий (-ая) по адресу <strong>{$faktadress->adressfull|upper}</strong>, далее именуемый (-ая)
    «Заёмщик»,
    находясь
    в здравом уме и
    ясной памяти,
    действуя добровольно в своих интересах, от своего имени, с другой стороны, далее совместно именуемые «Стороны», на
    условиях того, что Заёмщик уведомлён Заимодавцем о том, что настоящий Договор микрозайма состоит из двух частей:
    «Общих условий Договора микрозайма» и «Индивидуальных условий Договора микрозайма», изложенных в настоящем Договоре
    микрозайма, Заёмщик предоставляет Заимодавцу акцепт «Общих условий Договора микрозайма», текст которых предоставлен
    Заимодавцем Заёмщику на сайте http://www.рукред.рф/ до подписания настоящего Договора микрозайма и заключает
    настоящий Договор микрозайма (далее – «Договор») на следующих Индивидуальных условиях.<br>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 1</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<br><br><br>
<h3><strong>1. ИНФОРМАЦИЯ О ЗАИМОДАВЦЕ</strong></h3><br>
<table border="1" style="width: 100%; font-size: 9px" cellpadding="5">
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.1. Полное наименование Заимодавца</td>
        <td style="width: 70%">Общество с ограниченной ответственностью Микрокредитная компания «Русское кредитное
            общество»
        </td>
    </tr>
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.2. Сокращённое наименование Заимодавца</td>
        <td style="width: 70%">ООО МКК «Русское кредитное общество»</td>
    </tr>
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.3. ОГРН</td>
        <td style="width: 70%">121770033532</td>
    </tr>
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.4. ИНН</td>
        <td style="width: 70%">9725055162</td>
    </tr>
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.5. КПП</td>
        <td style="width: 70%">772501001</td>
    </tr>
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.6. Юридический адрес:</td>
        <td style="width: 70%">117449, город Москва, вн.тер.г.муниципальный округ Академический, улица Винокурова, дом
            3, этаж/ком. 1/А,
            пом./ком. I/1-3,6-11
        </td>
    </tr>
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.7. Местонахождение:</td>
        <td style="width: 70%">117449, город Москва, улица Винокурова, дом 3, этаж/комната 1/А,
            помещение I
        </td>
    </tr>
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.8. Адрес электронной почты</td>
        <td style="width: 70%">client@rucred.ru</td>
    </tr>
    <tr style="width: 100%;">
        <td style="width: 30%; background-color: #b3b2ab">1.9. Интернет-сайт Заимодавца</td>
        <td style="width: 70%">http://www.рукред.рф/</td>
    </tr>
</table>
</div>
<div align="left">С 01 января 2017 года вступил в силу Федеральный закон от 03 июля 2016 года №230-ФЗ «О защите прав
    и законных
    интересов физических лиц при осуществлении деятельности по возврату просроченной задолженности и о внесении
    изменений в Федеральный закон «О микрофинансовой деятельности и микрофинансовых организациях», который, в т.ч. внёс
    следующие изменения в Федеральный закон от 02 июля 2010 года №151-ФЗ «О микрофинансовой деятельности и
    микрофинансовых организациях» (далее – «Закон №151-ФЗ»).<br><br>После возникновения просрочки исполнения
    обязательства
    заёмщика – физического лица по возврату суммы займа и (или)
    уплате причитающихся процентов микрофинансовая организация по договору потребительского займа, срок возврата
    потребительского займа по которому не превышает один год, вправе начислять заёмщику – физическому лицу неустойку
    (штрафы, пени) и иные меры ответственности только на не погашенную заёмщиком часть суммы основного долга (часть 2
    статьи 12.1 Закона №151-ФЗ).

</div>
<div>
    <br><br><br><br><br><br><br><br><br>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 2</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<div>
    <h3><strong>2. ИНДИВИДУАЛЬНЫЕ УСЛОВИЯ ДОГОВОРА МИКРОЗАЙМА</strong></h3><br>
    <table border="1" style="width: 100%; font-size: 9px" cellpadding="5">
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">Условие</td>
            <td style="width: 70%">Содержание условия</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.1. Сумма микрозайма или лимит микрозайма и порядок его
                изменения
            </td>
            <td style="width: 70%"><strong>{$amount|number_format:0:',':' '} ({$amount_string|upper})</strong> рублей
                <strong>00</strong>
                копеек
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.2. Срок действия Договора микрозайма, и срок возврата
                микрозайма
            </td>
            <td style="width: 70%" align="left">Договор вступает в силу с даты его подписания Сторонами и действует
                до полного
                выполнения Сторонами своих обязательств по Договору.
                Срок возврата микрозайма – <strong>не позднее {$probably_return_date|date} года включительно</strong>
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.3. Валюта, в которой предоставляется микрозаём</td>
            <td style="width: 70%">Рубли Российской Федерации</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.4. Процентная ставка (процентные ставки) в процентах
                годовых, а при применении переменной процентной ставки ‒ порядок её определения, соответствующий
                требованиям Федерального закона от 21 декабря 2013 года №353-ФЗ «О потребительском кредите (займе)», её
                значение на дату предоставления заёмщику Индивидуальных условий
            </td>
            <td style="width: 70%" align="left"><strong>{$percents}%
                    ({$percents_per_year|upper}{if $second_part_percents|upper} ЦЕЛЫХ И {$second_part_percents|upper} ТЫСЯЧНЫХ
                <br>ПРОЦЕНТОВ{/if})</strong> годовых
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.5. Порядок определения курса иностранной валюты при
                переводе денежных средств кредитором третьему лицу, указанному заёмщиком
            </td>
            <td style="width: 70%">Не применимо</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.5.1. Указание на изменение суммы расходов Заёмщика, при
                увеличении используемой в Договоре, переменной процентной ставки микрозайма на один процентный пункт,
                начиная со второго очередного платежа на ближайшую дату после предполагаемой даты заключения Договора
            </td>
            <td style="width: 70%">Не применимо</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.6. Количество, размер и периодичность (сроки) платежей
                Заёмщика по Договору или порядок определения этих платежей
            </td>
            <td style="width: 70%" align="left">Количество ежемесячных
                платежей:
                <strong>{if $loan_type == 1}1 (ОДИН){elseif $loan_type == 2}5 (ПЯТЬ){else}11 (ОДИННАДЦАТЬ){/if}</strong><br>Размер
                и периодичность (сроки)
                платежей Заёмщика по Договору определяются Правилами предоставления и
                обслуживания микрозаймов. Платежи производятся ежемесячно в соответствии с Графиком платежей являющимся
                неотъемлемой частью Договора (Приложение №1)
            </td>
        </tr>
    </table>
</div>
<div>

</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 3</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<div>
    <h3><strong>2. ИНДИВИДУАЛЬНЫЕ УСЛОВИЯ ДОГОВОРА МИКРОЗАЙМА</strong></h3><br>
    <table border="1" style="width: 100%; font-size: 9px" cellpadding="5">
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">Условие</td>
            <td style="width: 70%">Содержание условия</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.7. Порядок изменения количества, размера и периодичности
                (сроков) платежей Заёмщика при частичном досрочном возврате микрозайма
            </td>
            <td style="width: 70%" align="left">Заёмщик вправе погасить микрозаём досрочно полностью или частично без
                предварительного уведомления Займодавца.
                При частично досрочном возврате микрозайма сумма процентов пересчитывается и вносятся соответствующие
                изменения, которые оформляются новой редакцией в виде Дополнительного соглашения к Договору и Примерного
                графика платежей.
                В случае досрочного возврата всей суммы микрозайма или её части Заёмщик обязан уплатить Заимодавцу
                проценты по Договору на возвращаемую сумму микрозайма включительно до дня фактического возврата
                соответствующей суммы микрозайма или её части
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.8. Способы исполнения Заёмщиком денежных обязательств по
                Договору
            </td>
            <td style="width: 70%" align="left">Погашение микрозайма, уплата процентов за пользование микрозаймом
                осуществляется в
                соответствии с Общими условиями микрозайма:
                Исполнение обязательств по договору осуществляется следующими способами:<br>
                - Путём перечисления денежных средств на банковский счёт Заимодавца по реквизитам, указанным в Договоре;<br>
                - Посредством безналичного перечисления работодателем по распоряжению Заёмщика части заработной платы в
                счёт погашения задолженности по Договору микрозайма
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.8.1. Бесплатный способ исполнения Заёмщиком обязательств
                по Договору
            </td>
            <td style="width: 70%">Посредством безналичного перечисления работодателем по распоряжению Заёмщика части
                заработной платы в счёт погашения задолженности по Договору микрозайма
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.9. Обязанность заёмщика заключить иные договоры
            </td>
            <td style="width: 70%">Не применимо</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.10. Обязанность заёмщика по предоставлению обеспечения
                исполнения обязательств по договору и требования к такому обеспечению
            </td>
            <td style="width: 70%">Не применимо</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.11. Цели использования Заёмщиком микрозайма
            </td>
            <td style="width: 70%"> {if $loan->reason_flag == 0}&#x2611;{else}&#10065;{/if} На неотложные
                нужды<br> {if $loan->reason_flag == 1}&#x2611;{else}&#10065;{/if} На рефинансирование обязательств перед
                третьими лицами
            </td>
        </tr>
    </table>
</div>
<div>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 4</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<div>
    <h3><strong>2. ИНДИВИДУАЛЬНЫЕ УСЛОВИЯ ДОГОВОРА МИКРОЗАЙМА</strong></h3><br>
    <table border="1" style="width: 100%; font-size: 9px" cellpadding="5">
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">Условие</td>
            <td style="width: 70%">Содержание условия</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.12. Ответственность Заёмщика за ненадлежащее исполнение
                условий Договора размер неустойки (штрафа, пени) или порядок их определения
            </td>
            <td style="width: 70%" align="left">При невыполнении или ненадлежащем
                исполнении обязательств по погашению микрозайма и/или уплате процентов в срок превышающий 7 (Семь)
                календарных дней Заёмщик должен уплачивать Заимодавцу неустойку в виде пени из расчёта 0,055% (Ноль
                целых пятидесяти пяти тысячных процента) , за каждый день просрочки (начиная с восьмого дня) от
                непогашенной им части суммы основного долга до полного погашения просроченной задолженности, но не более
                20% (Двадцати процентов) годовых в соответствии с положениями п.21 ст.5 Федерального закона от
                21.12.2013 года №353-ФЗ «О потребительском кредите (займе).
                При наличии согласия Заёмщика о перечислении Работодателем части причитающейся ему заработной платы в
                счёт погашения задолженности по Договору, в случае отсутствия внесения очередного платежа в соответствии
                с Графиком платежей в течение 7 (Семи) календарных дней, Заёмщик обязан самостоятельно внести денежные
                средства в счёт погашения образовавшейся задолженности. В противном случае Заимодавец использует своё
                право на начисление неустойки в виде пени из, расчёта 0,055% (Ноль целых пятидесяти пяти тысячных
                процента) за каждый день просрочки от не погашенной им части суммы основного долга до полного погашения
                просроченной задолженности, но не более 20% (Двадцати процентов) годовых в соответствии с положениями
                п.21 ст.5 Федерального закона от 21.12.2013 года №353–ФЗ «О потребительском кредите (займе).
                В случае нарушения или ненадлежащего исполнения обязательств по Договору Заёмщик возмещает Заимодавцу
                причинённые убытки, предусмотренные условиями Договора. Убытки взыскиваются в полной сумме сверх
                неустойки.

            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.13. Условие об уступке кредитором (Заимодавцем) третьим
                лицам прав (требований) по договору
            </td>
            <td style="width: 70%" align="left">Согласие Заёмщика получено.
                Заимодавец вправе полностью или частично уступить свои права (требования) по Договору, а также по иным
                договорам, связанным с обеспечением возврата займа, только:<br>- юридическому лицу, осуществляющему
                профессиональную деятельность по предоставлению потребительских
                займов;<br>- юридическому лицу, осуществляющему деятельность по возврату просроченной задолженности
                физических лиц
                в качестве основного вида деятельности;<br>- специализированному финансовому обществу или физическому
                лицу, указанному в письменном согласии
                Заёмщика, полученном Заимодавцем после возникновения у Заёмщика просроченной задолженности по Договору
            </td>
        </tr>
    </table>
</div>
<div>
    <br><br><br><br><br>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 5</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<div>
    <h3><strong>2. ИНДИВИДУАЛЬНЫЕ УСЛОВИЯ ДОГОВОРА МИКРОЗАЙМА</strong></h3><br>
    <table border="1" style="width: 100%; font-size: 9px" cellpadding="5">
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">Условие</td>
            <td style="width: 70%">Содержание условия</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.14. Согласие заёмщика с общими условиями договора
            </td>
            <td style="width: 70%" align="left">С содержанием Общих условий получения и обслуживания микрозайма
                Заёмщик ознакомлен и
                согласен:<br><br>Подпись Заёмщика: _____________________ <img
                        src="{$config->root_url}/theme/manager/html/pdf/i/warning.png" style="height: 12px;"><br><br>Общие
                условия Договора в печатном
                варианте выдаются Заимодавцем по требованию Заёмщика<br>
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.15. Услуги, оказываемые Заимодавцем Заёмщику за
                отдельную плату и необходимые для заключения Договора, их цена или порядок её определения (при наличии),
                а также подтверждение согласия Заёмщика на их оказание
            </td>
            <td style="width: 70%">Не применимо
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.16. Способ обмена информацией между Заимодавцем и
                Заёмщиком
            </td>
            <td style="width: 70%" align="left">Предназначенная для Заёмщика корреспонденция/информация может по
                усмотрению
                Заимодавца осуществляться любым из следующих способов: посредством телефонной, факсимильной, почтовой
                связи и/или может направлять информацию Заёмщику посредством электронной почты и Смс-сообщений,
                мессенджеров.
                При этом датой получения информации считается дата отправки Заимодавцем соответствующего сообщения
                Заёмщику в зависимости от способа направления информации.
                Корреспонденция, направленная в адрес Стороны и возвращённая с почтовой отметкой об отсутствии адресата,
                выбытие адресата, отказ от получения, считается направленной по надлежащему адресу в случае, если
                Сторона не была заранее уведомлена об изменении адреса другой Стороны

            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.17. Порядок предоставления микрозайма
            </td>
            <td style="width: 70%" align="left">Микрозаём предоставляется Заёмщику в день заключения Договора в
                безналичной форме, путём перечисления денежных средств:<br><br>☐ на расчётный счёт Заёмщика / реквизиты
                банковской карты Заёмщика, указанные в п.7 Договора<br>☒ по реквизитам, указанным Заёмщиком в Заявлении
                от {$probably_start_date|date} года о перечислении
                заёмных денежных средств<br>☐ по реквизитам, указанным Заёмщиком в Заявлении
                от {$probably_start_date|date} года о перечислении
                заёмных денежных средств в счёт погашения задолженности на счёт третьего лица
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.18. Согласие Заёмщика на предоставление Заимодавцем
                информации третьим лицам
            </td>
            <td style="width: 70%" align="left">Заёмщик согласен на предоставление Заимодавцем информации третьим
                лицам в объёме,
                порядке и на условиях, предусмотренных Общими условиями Договора
            </td>
        </tr>
    </table>
</div>
<div>
    <br><br><br>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 6</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<div>
    <h3><strong>2. ИНДИВИДУАЛЬНЫЕ УСЛОВИЯ ДОГОВОРА МИКРОЗАЙМА</strong></h3><br>
    <table border="1" style="width: 100%; font-size: 9px" cellpadding="5">
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">Условие</td>
            <td style="width: 70%">Содержание условия</td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.19. Заявления Заёмщика
            </td>
            <td style="width: 70%" align="left">Заёмщик является полностью дееспособным лицом, под опекой,
                попечительством, а также
                патронажем не состоит, на учёте в психоневрологическом и наркологическом диспансерах не состоит, по
                состоянию здоровья может самостоятельно осуществлять и защищать свои права и исполнять обязанности, не
                страдает заболеваниями, препятствующими осознавать суть подписываемого Договора и обстоятельства его
                заключения, у него отсутствуют обстоятельства, вынуждающие совершить данную сделку.
                Заёмщик подтверждает, что Договор заключается добровольно и на взаимовыгодных условиях, Сторонам
                Договора не поступают угрозы и не совершаются насильственные действия, направленные на понуждение их к
                заключению Договора, Заёмщик полностью дееспособен/правоспособен, способен понимать значение своих
                действий и/или руководить ими, а также понимать юридические последствия подписания Договора

            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">2.20. Правила определения подсудности споров, между
                Заёмщиком и Банком, вытекающие из Договора или в связи с ним
            </td>
            <td style="width: 70%" align="left">Споры, не урегулированные Договором и иными соглашениями Сторон,
                подлежат
                рассмотрению в соответствии с действующим законодательством Российской Федерации.
                Иски Заёмщика к Заимодавцу о защите прав потребителей предъявляются в соответствии с законодательством
                Российской Федерации.
                Территориальная подсудность дела по иску Заимодавца к Заёмщику, который возник или может возникнуть в
                будущем в любое время до принятия дела судом к своему производству рассматриваются Симоновском районном
                суде города Москвы по адресу: 115280 город Москва, улица Восточная, дом 2, строение 6, по месту
                нахождения Заимодавца, за исключением исков Заимодавца к Заёмщику, подлежащих рассмотрению по правилам
                исключительной подсудности

            </td>
        </tr>
    </table>
</div>
<div>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 7</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<div>
    <h3><strong>3. ИНФОРМАЦИЯ О ПОЛНОЙ СТОИМОСТИ МИКРОЗАЙМА, РАССЧИТАННОЙ НА ОСНОВАНИИ ПРИМЕРНОГО ГРАФИКА ПЛАТЕЖЕЙ ПО
            МИКРОЗАЙМУ</strong></h3><br>
    <table border="1" style="width: 100%; font-size: 9px" cellpadding="4">
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">3.1. Основные параметры микрозайма
            </td>
            <td style="width: 70%" align="left">(01) Сумма микрозайма: <strong>{$amount|number_format:0:',':' '}
                    ({$amount_string|upper})</strong> рублей
                <strong>00</strong> копеек<br>(02) Срок микрозайма: <strong>{$period|escape}</strong>
                (<strong>{$period_str|upper}</strong>)
                {$period|plural:'день':'дней':'дня'}<br>(03)
                Процентная
                ставка по микрозайму в день: <strong>{$percent|number_format:3:',':' '}%
                    ({$percents_per_day_str_part_one|upper} {if $percents_per_day_str_part_two|upper} ЦЕЛЫХ И {$percents_per_day_str_part_two|upper} СОТЫХ ПРОЦЕНТА{/if})</strong><br>(04) Полная
                стоимость микрозайма в валюте
                микрозайма: <br><strong>{$payment_schedule['result']['all_loan_percents_pay']|number_format:2:',':' '}</strong>
                (<strong>{$all_percents_string_part_one|upper}</strong>)
                рублей
                <strong>{if $all_percents_string_part_two}{$all_percents_string_part_two}{else}00{/if}</strong>
                копеек<br>(05)
                Полная стоимость микрозайма в процентах
                годовых: <strong>{$percents}%
                    ({$percents_per_year|upper} {if $second_part_percents|upper} ЦЕЛЫХ И {$second_part_percents|upper} ТЫСЯЧНЫХ ПРОЦЕНТОВ{/if})</strong>
                годовых<br>(06) Общая сумма процентов за период пользования
                микрозаймом:
                <br><strong>{$payment_schedule['result']['all_loan_percents_pay']|number_format:2:',':' '}</strong>
                (<strong>{$all_percents_string_part_one|upper}</strong>)
                рублей
                <strong>{if $all_percents_string_part_two}{$all_percents_string_part_two}{else}00{/if}</strong>
                копеек<br>
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">3.2. Услуги, оказываемые Заимодавцем за отдельную плату и
                необходимые для заключения Договора микрозайма
            </td>
            <td style="width: 70%">Отсутствуют
            </td>
        </tr>
        <tr style="width: 100%;">
            <td style="width: 30%; background-color: #b3b2ab">3.3. Согласие Заёмщика на оказание необходимых услуг,
                предшествующих заключению Договора микрозайма
            </td>
            <td style="width: 70%">Не применимо
            </td>
        </tr>
    </table>
</div>
<div>
    <br><br><br><br><br><br><br><br><br>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 8</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<h3><strong>4. ПРИМЕРНЫЙ ГРАФИК ПЛАТЕЖЕЙ</strong></h3><br>
<table border="1" style="width: 100%; font-size: 8px;" cellpadding="4">

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
    <br><br><br><br><br><br><br>
</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 9</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
{else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<div>
</div>
<h3><strong>5. ЗАВЕРЕНИЯ ЗАЁМЩИКА</strong></h3><br>
<hr style="width: 100%; size: 5px">
<div>

</div>
<div>В соответствии с требованиями действующего законодательства до подписания Индивидуальных условий Договора
    микрозайма
    Заёмщик заверяет Заимодавца, что:<br><br>(01) предварительно ознакомлен(-на) С Общими условиями выдачи микрозайма,
    размещёнными на официальном сайте
    Заимодавца;<br><br>(02) ознакомлен (-на) с размером полной стоимости микрозайма, перечнем и размерами
    платежей;<br><br>(03)
    уведомлён (-на), что, если в течение одного года общий размер платежей по всем имеющимся у Заёмщика на дату
    обращения к Заимодавцу по Договорам займов в сторонних организациях, включая платежи по предоставляемому микрозайму,
    будет превышать 50% (Пятьдесят процентов) доходов Заёмщика, у Заёмщика существует риск неисполнения обязательств по
    Договору микрозайма.<br><br>(04) подтверждает свои намерения о неукоснительном надлежащем исполнении всех принятых
    на
    себя обязательств по
    Договору.<br><br>(05) подтверждает, что условия Договора микрозайма полностью соответствуют его интересам.
</div>
<h3><strong>6. ПРОЧИЕ ПОЛОЖЕНИЯ</strong></h3><br>
<hr style="width: 100%; size: 5px">
<div>

</div>
<div><br><br><strong>6.1.</strong> Договор составлен в двух подлинных экземплярах, имеющих равную юридическую силу, по
    одному
    для каждой из Сторон.<br><br><strong>6.2.</strong> При заключении Договора Заимодавец довёл до Заёмщика также
    информацию
    о полной стоимости
    микрозайма, перечень и
    размеры платежей, включённых и не включённых в расчёт полной стоимости микрозайма, перечень и размеры платежей,
    связанных с несоблюдением им условий Договора.<br><br><strong>6.3.</strong> Заёмщик подтверждает, что получил от
    Заимодавца информацию о предстоящих платежах в
    соответствии с Договором.<br><br><strong>6.4.</strong> Заёмщик, подписывая Договор, обязуется направить в адрес
    Работодателям (иного Работодателя в
    случае его смены)
    заявление о перечислении части причитающейся заработной платы на счёт Заимодавца в счёт погашения задолженности по
    микрозайму в соответствии с Графиком платежей.<br><br><strong>6.5.</strong> Приложение к Договору микрозайма:<br>-
    Приложение №1. График платежей по микрозайму
</div>
<h3><strong>7. РЕКВИЗИТЫ И ПОДПИСИ СТОРОН</strong></h3><br>
<hr style="width: 100%; size: 5px">
<div>

</div>
<table>
    <tr>
        <td style="width: 45%">ЗАИМОДАВЕЦ</td>
        <td style="width: 10%"></td>
        <td style="width: 45%">ЗАЁМЩИК</td>
    </tr>
    <tr>
        <td style="width: 45%"><strong>Общество с ограниченной ответственностью Микрокредитная компания «Русское
                кредитное общество»</strong></td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper}</strong></td>
    </tr><br>
    <tr>
        <td style="width: 45%"><u>юридический адрес</u>
            117449, город Москва, вн.тер.г.муниципальный округ Академический, улица Винокурова, дом 3, этаж/ком. 1/А,
            пом./ком. I/1-3,6-11
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>паспорт гражданина Российской Федерации</u>
            {$passport_serial} {$passport_number} выдан {$passport_issued} {$passport_date|date} года
        </td>
    </tr>
    <tr>
        <td style="width: 45%"><u>фактический адрес местонахождения</u>
            117449, город Москва, улица Винокурова, дом 3, этаж/комната 1/А, помещение I
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>адрес регистрации</u>
            {$regadress->adressfull}
        </td>
    </tr>
    <tr>
        <td style="width: 45%">ОГРН 121770033532</td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>фактический адрес проживания</u>
            {$faktadress->adressfull}
        </td>
    </tr>
</table>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%; page-break-after: always; font-size: 8px" border="1">
            <tr style="width: 100%">
                <td style="width: 8%; height: 30px" align="center">
                    <div><strong style="color: #b3b2ab;padding-top: 2px">СТР. 10</strong></div>
                </td>
                <td style="width: 37%" align="center">
                    <div><span style="color: #b3b2ab">ПОДПИСЬ</span></div>
                </td>
                <td style="width: 40%" align="center">
                    <div><span style="color: #b3b2ab">ФИО ПОЛНОСТЬЮ СОБСТВЕННОРУЧНО</span></div>
                </td>
                <td style="width: 15%;" align="center">
                    <div><span style="color: #b3b2ab">ДАТА</span></div>
                </td>
            </tr>
        </table>
    </footer>
    {else}
    <table style="color: #002688; page-break-after: always;  font-size: 8px!important; font-style: italic; border: 0.25pt solid #002088; width: 50%"
           cellpadding="1" cellspacing="2">
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
<table>
    <tr>
        <td style="width: 45%">ИНН 9725055162</td>
        <td style="width: 10%"></td>
        <td style="width: 45%">ИНН {$inn}</td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%">КПП 772501001</td>
        <td style="width: 10%"></td>
        <td style="width: 45%"></td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%"><u>платёжные реквизиты:</u>
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%"><u>платёжные реквизиты:</u>
        </td>
    </tr>
    <br>
    <tr>
        <td style="width: 45%">р/с {$settlement->payment} в {$settlement->name}, БИК {$settlement->bik},
            к/с {$settlement->cors}
        </td>
        <td style="width: 10%"></td>
        <td style="width: 45%">р/с {$requisite->number} в {$requisite->name} БИК {$requisite->bik} к/с {$requisite->correspondent_acc}
        </td>
    </tr>
    <br>
    <br>
    <tr>
        {if !isset($code_asp->code)}
            <td style="width: 45%">
                __________________/<strong>А.В.Лоскутов/</strong>
            </td>
            <td style="width: 10%">
            </td>
            <td style="width: 45%">
                __________________/<strong>{$firstname|mb_substr:0:1}.{$patronymic|mb_substr:0:1}.{$lastname}/</strong>
            </td>
        {else}
            <td style="width: 45%">
                <table style="color: #002688; font-style: italic; border: 0.25pt solid #002088;" cellspacing="5">
                    <tr>
                        <td>ДОКУМЕНТ ПОДПИСАН ЭЛЕКТРОННОЙ ПОДПИСЬЮ</td>
                    </tr>
                    <tr>
                        <td>Подписант: А.В. Лоскутов</td>
                    </tr>
                    <tr>
                        <td>Дата подписания: {$rucred_asp->created|date} {$rucred_asp->created|time}(МСК)</td>
                    </tr>
                    <tr>
                        <td>ID подписания: {$rucred_asp->uid}</td>
                    </tr>
                    <tr>
                        <td>Система ЭДО: Рестарт.Онлайн</td>
                    </tr>
                </table>
            </td>
            <td style="width: 10%">
            </td>
            <td style="width: 45%">
                <table style="color: #002688; font-style: italic; border: 0.25pt solid #002088;"
                       cellpadding="1" cellspacing="5">
                    <tr>
                        <td>ДОКУМЕНТ ПОДПИСАН ЭЛЕКТРОННОЙ ПОДПИСЬЮ</td>
                    </tr>
                    <tr>
                        <td>Подписант: {$firstname} {$patronymic} {$lastname}</td>
                    </tr>
                    <tr>
                        <td>Дата подписания: {$code_asp->created|date} {$code_asp->created|time}(МСК)</td>
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
            </td>
        {/if}
    </tr>
</table>