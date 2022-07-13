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
                        src="https://1.downloader.disk.yandex.ru/preview/e48f9242ee8c50fc1caffad3fc788ebf1560f24984966c741fccf6cea0d3f5b2/inf/Xd_3SKwR4Nnc1GEpPvESC1xIis-Txp_N0bP6o8BzpSgLvRsD1PKSHZdiCNG5_FxbQ4LQffjPWKbcD3ZgJRg7-A%3D%3D?uid=1038001787&filename=lk.png&disposition=inline&hash=&limit=0&content_type=image%2Fpng&owner_uid=1038001787&tknv=v2&size=1920x937"></a>
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
            <img src="https://3.downloader.disk.yandex.ru/preview/d54d95d23237c2b1d6925f69e0ca769d007e465148172b938b44d4a6d3392a3b/inf/4rfzRgZ_ncTqQpZb05m6jXE8oLsftGr-4hOAflIYKjNQyP4g1_t31nX16GmJc9fH-PVdEcTsewjPS8W4V2tR5Q%3D%3D?uid=1038001787&filename=first-btn.jpg&disposition=inline&hash=&limit=0&content_type=image%2Fjpeg&owner_uid=1038001787&tknv=v2&size=1920x937">
            <img src="https://1.downloader.disk.yandex.ru/preview/7a033bb2cc25f32fe8afd3fe2f6b073c1eea0ef6da8dfe0dabe6c0ceba97bd57/inf/kjGKNmYqXJC7kEBs1_kPd3E8oLsftGr-4hOAflIYKjOPiJUxyYoeARvuVhb4VXpqynufBoJeWzlryzVFU2OPKQ%3D%3D?uid=1038001787&filename=download-googleplay.png&disposition=inline&hash=&limit=0&content_type=image%2Fpng&owner_uid=1038001787&tknv=v2&size=1920x937">
            <img src="https://3.downloader.disk.yandex.ru/preview/088e20e7712d433b57221f816b338b77760eb595634988f88f4aa3ffb4a5c20c/inf/8DCFYXH9VrIyYyDkq2TGo3E8oLsftGr-4hOAflIYKjNF8RbBnb0VHxUZidF9n_1EC_6YgdnSETMBuqsgJOmjog%3D%3D?uid=1038001787&filename=restart.png&disposition=inline&hash=&limit=0&content_type=image%2Fpng&owner_uid=1038001787&tknv=v2&size=1920x937">
            <img src="https://2.downloader.disk.yandex.ru/preview/c0c5666ee930b22ab7cccc268096f9822ff3fc436e818cba764c2ad6912ea9d5/inf/4hmJLUQdojss-CL0QrTsMFxIis-Txp_N0bP6o8BzpSihbD1UniVS032zDWpyNxOdt2GgU27aD9cM1x3XVnrdFw%3D%3D?uid=1038001787&filename=logo.png&disposition=inline&hash=&limit=0&content_type=image%2Fpng&owner_uid=1038001787&tknv=v2&size=1920x937">
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