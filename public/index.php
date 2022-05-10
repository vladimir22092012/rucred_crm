<?php

session_start();

$start_time = microtime(true);

require __DIR__ . '/../vendor/autoload.php';

try {
    $view = new IndexController();

    if (($res = $view->fetch()) !== false) {
        if ($res === 403) {
            header("http/1.0 403 Forbidden");
            $_GET['page_url'] = '403';
            $_GET['module'] = 'ErrorController';
            print $view->fetch();
        } else {
            // Выводим результат
            header("Content-type: text/html; charset=UTF-8");
            print $res;
        }
    } else {
        // Иначе страница об ошибке
        header("http/1.0 404 not found");

        // Подменим переменную GET, чтобы вывести страницу 404
        $_GET['page_url'] = '404';
        $_GET['module'] = 'ErrorController';
        print $view->fetch();
    }
} catch (Exception $e) {
    echo __FILE__ . ' ' . __LINE__ . '<br /><pre>';
    var_dump($e);
    echo '</pre><hr />';
}

$end_time = microtime(true);

if (!empty($view->is_developer)) {
    $exec_time = round(($end_time - $start_time) * 1000) / 1000;
    echo '<div style="position:fixed;right:0;bottom:0;background:#222;color:#fff;padding:10px;;width:200px;height:50px;z-index:999;">' . $exec_time . '</div>';
}
