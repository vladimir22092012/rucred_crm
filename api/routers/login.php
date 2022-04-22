<?php

// Роутер
function route($method, $urlData, $formData, $core, $headers) {

    // Получение информации
    // POST /auth
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