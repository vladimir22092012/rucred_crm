<?php

chdir('..');
require 'autoload.php';

class IndexApi extends Core
{
    private $method;
    private $urlData;
    private $formData;
    private $router;

    public function __construct()
    {
    	parent::__construct();

        $this->prepare();

        // Подключаем файл-роутер и запускаем главную функцию
        include_once 'api/routers/' . $this->router . '.php';
        $this->run();
    }

    public function run()
    {
        $this->route($this->method, $this->urlData, $this->formData);
    }

    public function prepare()
    {
        // Определяем метод запроса
        $method = $_SERVER['REQUEST_METHOD'];

        // Получаем данные из тела запроса
        $this->formData = $this->getFormData($method);

        // Разбираем url
        $url = (isset($_GET['q'])) ? $_GET['q'] : '';
        $url = rtrim($url, '/');
        $urls = explode('/', $url);

        // Определяем роутер и url data
        $this->router = $urls[0];
        $this->urlData = array_slice($urls, 1);


    }

    // Получение данных из тела запроса
    public function getFormData($method) {

        // GET или POST: данные возвращаем как есть
        if ($method === 'GET') return $_GET;
        if ($method === 'POST') return $_POST;

        // PUT, PATCH или DELETE
        $data = array();
        $exploded = explode('&', file_get_contents('php://input'));

        foreach($exploded as $pair) {
            $item = explode('=', $pair);
            if (count($item) == 2) {
                $data[urldecode($item[0])] = urldecode($item[1]);
            }
        }

        return $data;
    }
}
