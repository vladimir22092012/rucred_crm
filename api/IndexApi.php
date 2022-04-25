<?php

error_reporting(-1);
ini_set('display_errors', 'On');

require_once( __DIR__ . '/../vendor/autoload.php');

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/api/test/{id:\d+}', 'Api\routers\Test3@get');
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
    $r->addGroup('/api', function (FastRoute\RouteCollector $r) {
        $r->addRoute('POST', '/test', 'Api\routers\Test@test');
        $r->addRoute('GET', '/do-another-thing', 'handler');
        $r->addRoute('GET', '/do-something-else', 'handler');
        $r->post('/post-route', 'Api\routers\Test3@tme2');
    });
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
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
        $vars = $routeInfo[2];
        //require $handler;
        list($class, $method) = explode("@", $handler, 2);
        call_user_func_array(array(new $class, $method), $vars);
        //var_dump($handler);
        // ... call $handler with $vars
        break;
}