<?php

function core_autoload($classname)
{
    define('ROOT', rtrim(__DIR__, '\\/'));

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
