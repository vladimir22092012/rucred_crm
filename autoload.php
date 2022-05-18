<?php

use JetBrains\PhpStorm\NoReturn;

function core_autoload($classname)
{
    if (file_exists(__DIR__ .'/core/'.$classname.'.php'))
        require __DIR__ .'/core/'.$classname.'.php';
    if (file_exists(__DIR__ .'/models/'.$classname.'.php'))
        require __DIR__ .'/models/'.$classname.'.php';
    if (file_exists(__DIR__ .'/controllers/'.$classname.'.php'))
        require __DIR__ .'/controllers/'.$classname.'.php';
    if (file_exists(__DIR__ .'/scorings/'.$classname.'.php'))
        require __DIR__ .'/scorings/'.$classname.'.php';
}
spl_autoload_register('core_autoload');

function response_json($data) {
    header("Content-type: application/json; charset=UTF-8");
    header("Cache-Control: must-revalidate");
    header("Pragma: no-cache");
    header("Expires: -1");

    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}
