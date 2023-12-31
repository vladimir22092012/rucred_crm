<table>
    <tr style="width: 100%">
        <td style="width: 50%"><img src="{$config->root_url}/theme/manager/html/pdf/i/RKO.png" style="height: 80px">
        </td>
    </tr>
</table>
<div></div>
<h3 align="left"><strong>СОГЛАСИЕ НА ОБРАБОТКУ ПЕРСОНАЛЬНЫХ ДАННЫХ</strong></h3>
<div></div>
<div align="justify">Я, <strong>{$lastname|upper} {$firstname|upper} {$patronymic|upper} {$birth|date_format:'%d.%m.%Y'}</strong>
    года рождения, место
    рождения <strong>{$birth_place|upper}</strong>, телефон: <strong>{$phone_mobile}</strong>
</div>
<div align="justify"><br>в соответствии с Федеральным законом от 27.07.2006 года № 152-ФЗ «О персональных данных» для
    реализации следующих
    целей: <br>(01) заключения и исполнения договоров микрозайма;<br>(02) предоставления скидок и льготных условий по
    договорам микрозайма;<br>(03) получения предложений продуктов и услуг Общества;<br>(04) получения предложений
    продуктов и услуг контрагентов Общества, приёма на обслуживание в порядке,
    предусмотренном Правилами предоставления и обслуживания микрозаймов Обществом<br><br><strong>принимаю решение и даю
        согласие</strong>
    <br><br>на обработку, любое действие (операцию) или совокупность действий (операций), совершаемых с
    использованием средств автоматизации или без использования таких средств, включая сбор, запись, систематизацию,
    накопление, хранение, уточнение (обновление, изменение), извлечение, использование, передачу (предоставление,
    доступ), обезличивание, блокирование, удаление, уничтожение) Обществу с ограниченной ответственностью Микрокредитная
    компания «Русское кредитное общество» (ОГРН 121700334532, ИНН9725055162, регистрационный номер в государственном
    реестре микрофинансовых организаций 2103045009730, место нахождения постоянно действующего исполнительного органа 117449, город Москва, улица
    Винокурова, дом 3, этаж/комната 1/А, помещение I, тел. +7 (495)
    803-33-30, официальный сайт http://РуКред.рф/), ранее и далее именуемое Общество, следующих персональных данных<br>
    • фамилия, имя, отчество (при наличии) (в том числе прежние фамилии, имена и (или) отчества (при наличии), дата,
    место и причина изменения в случае их изменения);<br>
    • число, месяц, год рождения;<br>
    • место рождения;<br>
    • сведения о гражданстве (в том числе предыдущие гражданства, иные гражданства);<br>
    • сведения об образовании;<br>
    • сведения о профессиональной переподготовке и (или) повышении квалификации;<br>
    • сведения о наличии или отсутствии
    судимости;<br>
    • сведения о трудовой деятельности (включая работу по совместительству, предпринимательскую и иную
    деятельность);<br>
    • сведения о семейном положении, составе семьи и о близких родственниках (в том числе бывших);<br>
    • сведения о близких родственниках (отец, мать, братья, сестры и дети), а также супругах, в том числе бывших,
    постоянно проживающих за границей и (или) оформляющих документы для выезда на постоянное место жительства в другое
    государство (фамилия, имя, отчество (при наличии), с какого времени проживают за границей);<br>
    • отношение к
    воинской обязанности, сведения о воинском учете и реквизиты документов воинского учета;<br>
    • адрес и дата регистрации по месту жительства (месту пребывания), адрес фактического проживания;<br>
    • номер контактного телефона
    или сведения о других способах связи;<br>
    <div>

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
        <table style="border: 0.25pt solid #002088; width: 50%; font-size: 8px; page-break-after: always;"
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
                <td><strong>Система ЭДО:</strong><br>Рестарт.Онлайн</td>
                <td><img src="{$config->root_url}/theme/manager/html/pdf/i/Vector.png" style="height: 20px"></td>
            </tr>
        </table>
    {/if}
    <div>

    </div>
    • вид, серия, номер документа, удостоверяющего личность, дата выдачи, наименование органа, выдавшего его;<br>•
    реквизиты паспорта гражданина Российской Федерации, удостоверяющего личность гражданина Российской Федерации за
    пределами территории Российской Федерации (серия, номер, когда и кем выдан);<br>• реквизиты страхового свидетельства
    обязательного пенсионного страхования;<br>• идентификационный номер налогоплательщика;<br>• реквизиты страхового
    медицинского полиса обязательного медицинского страхования;<br>• реквизиты свидетельств государственной регистрации
    актов гражданского состояния;<br>• личная фотография;<br>• сведения, содержащиеся в трудовом договоре,
    дополнительных соглашениях к трудовому договору;<br>• сведения о ежегодных оплачиваемых отпусках и отпусках без
    сохранения денежного содержания;<br>• сведения о доходах, расходах, об имуществе и обязательствах имущественного
    характера, а также о доходах, расходах,
    об имуществе и обязательствах имущественного характера супруги (супруга) и несовершеннолетних детей;<br>• номер
    расчётного счета (номера расчётных счетов);<br>• номер банковской карты (номера банковских
    карт).
    <br><br><strong>Я осведомлён(а) и согласен(а)</strong> с тем, что в соответствии со статьёй 6 Федерального
    закона от
    27.07.2006 г. №152-ФЗ «О персональных данных» в случае заключения между мной и Заимодателем Договора микрозайма,
    Заимодатель вправе в течение действия Договора микрозайма осуществлять без моего (субъекта персональных данных)
    дополнительного согласия обработку персональных данных, так же указанных в Анкете-Заявлении, в целях исполнения
    Договора микрозайма, при этом Заимодатель в период действия Договора микрозайма не обязан прекращать обработку
    персональных данных, и не обязан уничтожать персональные данные, указанные в Анкете-Заявлении, в случае отзыва
    согласия на обработку персональных данных, данного Заимодателю в целях принятия решения о предоставлении мне
    микрозайма, заключения со мной Договора микрозайма и формирования данных о моем обращении, о моей кредитной истории
    у Заимодателя.<br><br>Я даю согласие Обществу на обработку персональных данных, указанных в настоящем согласии об
    обработке персональных
    данных и анкете-заявлении на получение микрозайма, в том числе на предоставление мне рекламной информации
    (продукции), в целях продвижения Займодателем своих услуг на рынке розничного бизнеса, путём осуществления прямых
    контактов со мной с помощью средств связи. Согласие на обработку Займодателем персональных данных, в целях
    продвижения Займодателем своих услуг действует до момента отзыва субъектом персональных данных данного согласия в
    письменном виде.<br><br>Настоящим подтверждаю, что &#10065; не
    являюсь /  &#10065; являюсь иностранным публичным должностным лицом,
    их супругами, близкими
    родственниками (родственниками по прямой восходящей и нисходящей линии (родителями и детьми, дедушкой, бабушкой и
    внуками), полнородными и неполно родными (имеющими общих отца или мать) братьями и сёстрами, усыновителями и
    усыновлёнными), должностным лицом публичных международных организаций, а также лиц, замещающих (занимающих)
    государственные должности Российской Федерации, должности членов Совета директоров Центрального банка Российской
    Федерации, должности федеральной государственной службы, назначение на которые и освобождение от которых
    осуществляются Президентом Российской Федерации или Правительством Российской Федерации, должности в Центральном
    банке Российской Федерации, государственных корпорациях и иных организациях, созданных Российской Федерацией на
    основании федеральных законов, включённые в перечни должностей, определяемые Президентом Российской
    Федерации.<br><br>Я согласен(а) с тем, что Общество может проверить достоверность предоставленных мною персональных
    данных, в том
    числе с использованием услуг других организаций, без уведомления меня об этом. Я подтверждаю, что, давая такое
    согласие, я действую по собственной воле и в своих интересах.<br><br>Данное согласие действует с момента
    предоставления в течение 5 (Пяти) лет. Данное согласие может быть отозвано в
    любой момент по моему письменному заявлению.
</div>
<div>

</div>
{if !isset($code_asp->code)}
    <footer>
        <table style="width: 100%;font-size: 8px" border="1">
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
    <table style="border: 0.25pt solid #002088; width: 50%; font-size: 8px;"
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
            <td><strong>Система ЭДО:</strong><br>Рестарт.Онлайн</td>
            <td><img src="{$config->root_url}/theme/manager/html/pdf/i/Vector.png" style="height: 20px"></td>
        </tr>
    </table>
{/if}
