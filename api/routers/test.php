<?php

// Роутер
function route($method, $urlData, $formData, $core) {

    // Получение информации
    // GET /test/{goodId}
    if ($method === 'GET' && count($urlData) === 1) {
        // Получаем id товара
        $goodId = $urlData[0];

        // Вытаскиваем товар из базы...
        $user = $core->users->get_user(181780);

        $res = [
            'method' => 'GET',
            'id' => $user,
            'good' => 'phone',
            'price' => 10000
        ];

        // Выводим ответ клиенту
        echo json_encode($res, JSON_PRETTY_PRINT);

        return;
    }

    if ($method === 'POST' && empty($urlData)) {
        // Добавляем товар в базу...

        $res = [
            'method' => 'POST',
            'id' => rand(1, 100),
            'formData' => $formData
        ];

        // Выводим ответ клиенту
        echo json_encode($res, JSON_PRETTY_PRINT);

        return;
    }

    // Возвращаем ошибку
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));

}