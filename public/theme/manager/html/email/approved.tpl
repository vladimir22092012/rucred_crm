<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta lang="ru">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>
<body>
<div style="display: flex">
    <div style="width: 35%">

    </div>
    <div style="width: 50%">
        <div align="left">
            <h1>Уважаемый<br>{$order->lastname} {$order->firstname} {$order->patronymic}!</h1>
        </div>
        <div align="left">
            Вы успешно заключили договор микрозайма с ООО МКК «Русское кредитное общество»<br>№ {$uid}
            от {$order->created|date} г.
        </div>
        <div>
            <br><br><br>
        </div>
        <div align="left">
            Настоящим направляем Вам список документов по микрозайму, с которым можно<br>ознакомиться по следующим
            ссылкам:
        </div>
        <br>
        <div align="left">
            <ul>
                <li><a target="_blank" href="{$individ_encrypt}">Договор № {$uid} от
                        {$order->created|date} г.</a></li>
                <br>
                <li><a target="_blank" href="{$graphic_encrypt}">График платежей по договору № {$uid}</a></li>
            </ul>
        </div>
        <br>
        <div align="left">
            <a target="_blank" style="text-decoration: none; color: white"
               href="https://xn--80aj6acdgc.xn--80asehdb/auth"><img
                        src="https://ie.wampi.ru/2022/07/13/lk.png"></a>
        </div>
        <br><br>
        <div align="left">
            Обращаем Ваше внимание, что данный микрозайм будет обслуживать Ваш работодатель.
        </div>
        <br><br>
        <div align="left" style="font-size: 12px;">
            Вы всегда можете задать нам любой вопрос, позвонив в службу поддержки по<br>номеру 8(800)123-45-67 или
            отправив обращение на почту <a target="blank" href="client@rucred.ru">client@rucred.ru</a>
        </div>
        <br>
        <div align="left">
            <img src="https://ic.wampi.ru/2022/07/13/first-btn.jpg" alt="first-btn.jpg" border="0">
            <img src="https://ic.wampi.ru/2022/07/13/download-googleplay388ab94714b0f951.png" alt="download-googleplay388ab94714b0f951.png" border="0">
            <img src="https://im.wampi.ru/2022/07/13/restart.png" alt="restart.png" border="0">
            <img src="https://im.wampi.ru/2022/07/13/logo.png" alt="logo.png" border="0">
        </div>
        <br>
        <div align="left" style="font-size: 12px;">
            <small style="color: #b3b2ab">Чтобы уведомления случайно не попали в нежелательную почту, добавьте,
                пожалуйста, client@rucred.ru в адресную книгу
            </small>
        </div>
    </div>
    <div style="width: 15%">

    </div>
</div>
</body>
</html>