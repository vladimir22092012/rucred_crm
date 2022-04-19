<?php

// Роутер
function route($method, $urlData, $formData) {

    // Получение информации
    // GET /test/{goodId}
    if ($method === 'GET' && count($urlData) === 1) {
        // Получаем id товара
        $goodId = $urlData[0];

        // Вытаскиваем товар из базы...

        // Выводим ответ клиенту
        echo json_encode(array(
            'method' => 'GET',
            'id' => $goodId,
            'good' => 'phone',
            'price' => 10000
        ));

        return;
    }

    // Возвращаем ошибку
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));

}