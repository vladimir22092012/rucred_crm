<?php

error_reporting(-1);
ini_set('display_errors', 'On');

require_once( __DIR__ . '/../vendor/autoload.php');

//Более красивый и информативный вывод ошибок
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

/* 
    Все запросы находятся в группе /api
    Роуты могут добавляться 2 способами:
    $r->post('/login', 'Api\routers\LoginApi@run');
    $r->addRoute('POST', '/test', 'Api\routers\Test@test');
    Метод запроса может быть любой(отработает только указанный). Можно указать несколько
    $r->addRoute(['GET', 'POST'], '/test', 'handler');
    Роуты поддерживают регулярные выражения
    /test/{id:\d+} - {id} должно быть числом (\d+)
    /articles/{id:\d+}[/{title}] - /{title} -необязательный элемент
*/

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addGroup('/api', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/test', 'Api\routers\Test@test');
        //Авторизация
        $r->post('/login', 'Api\routers\LoginApi@run');
        //Отправка смс (код)
        $r->post('/send/sms/code', 'Api\routers\SmsApi@sendCode');
        //Регистрация
        $r->post('/registration/stage/main', 'Api\routers\RegistrationApi@stageMain');
        $r->post('/registration/stage/personal', 'Api\routers\RegistrationApi@stagePersonal');
        $r->post('/registration/stage/passport', 'Api\routers\RegistrationApi@stagePassport');
        $r->post('/registration/stage/address', 'Api\routers\RegistrationApi@stageAddress');
        $r->post('/registration/stage/work', 'Api\routers\RegistrationApi@stageWork');
        //Аккаунт
        $r->post('/account/profile', 'Api\routers\AccountApi@getProfile');
        $r->post('/account/orders', 'Api\routers\AccountApi@getOrders');
        $r->post('/account/documents', 'Api\routers\AccountApi@getDocuments');
        $r->post('/account/photos', 'Api\routers\AccountApi@getFiles');
        $r->post('/account/photos/add', 'Api\routers\AccountApi@addFiles');
    });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = ($httpMethod == 'POST')? $_POST : $routeInfo[2];
        //Получаем класс & метод роута и вызываем его
        list($class, $method) = explode("@", $handler, 2);
        call_user_func_array(array(new $class, $method), array($vars));
        break;
}