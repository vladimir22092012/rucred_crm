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
        <div align="left" style="font-style: italic">
            <h1>Уважаемый<br>{$order->lastname} {$order->firstname} {$order->patronymic}!</h1>
        </div>
        <div align="left">
            Вы успешно заключили договор микрозайма с ООО МКК «Русское кредитное общество»<br>№ {$order->uid}
            от {$order->probably_start_date|date} г.
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
                <li><a target="_blank" href="{$individ_encrypt}">№ {$order->uid} от
                        {$order->probably_start_date|date} г.</a></li>
                <br>
                <li><a target="_blank" href="{$graphic_encrypt}">График платажей по договору № {$order->uid}</a></li>
            </ul>
        </div>
        <br>
        <div align="left">
            <a target="_blank" style="text-decoration: none; color: white"
               href="https://rucred.andr-dev.ru/auth"><img
                        src="https://avatars.mds.yandex.net/get-images-cbir/195643/gtwJuG2B_pfNEUyx0twycQ2252/ocr"></a>
        </div>
        <br><br>
        <div align="left">
            Обращаем Ваше внимание, что данный микрозайм будет обслуживать Ваш работодатель.
        </div>
        <br><br>
        <div align="left" style="font-size: 12px;">
            Вы всегда можете задать нам любой вопрос, позвонив в службу поддержки по<br>номеру 8 (800) 123-45-67 или
            отправив обращение на почту <a target="blank" href="restartonline@info.ru">restartonline@info.ru</a>
        </div>
        <br>
        <div align="left">
            <img src="https://avatars.mds.yandex.net/get-images-cbir/1818750/1jKDrNjFzvqFKuwtjQX2wA2308/ocr">
            <img src="https://avatars.mds.yandex.net/get-images-cbir/1646438/CPz7tmYuJjLug4WachHJKw2376/ocr">
            <img src="https://avatars.mds.yandex.net/get-images-cbir/1605006/RuX71NqQnlyvTTwDeK11MQ2353/ocr">
            <img src="https://avatars.mds.yandex.net/get-images-cbir/989668/LUu8p3NftHXBHq5WbmSLmg2340/ocr">
        </div>
        <br>
        <div align="left" style="font-size: 12px;">
            <small style="color: #b3b2ab">Чтобы уведомления случайно не попали в нежелательную почту, добавьте,
                пожалуйста, restartonline@info.ru в адресную книгу
            </small>
        </div>
    </div>
    <div style="width: 15%">

    </div>
</div>
</body>
</html>