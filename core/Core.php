<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Core
{
    public $is_developer = 0;

    private $classes = array(
        'collector_tags' => 'CollectorTags',
    );
    private static $objects = array();

    public function __construct()
    {
        if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], array('94.154.39.111', '94.154.39.244', '212.46.18.173'))) {
            $this->is_developer = 1;
        }
//      if (isset($_COOKIE['developer']) && $_COOKIE['developer'] == '4616')
//            $this->is_developer = 1;
//        if (isset($_GET['set_developer']))
//        {
//            setcookie('developer', $_GET['set_developer']);
//            header('Location: /');
//            exit;
//        }

        if ($this->is_developer) {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        }

        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $this->config->db_server,
            'database' => $this->config->db_name,
            'username' => $this->config->db_user,
            'password' => $this->config->db_password,
            'charset' => $this->config->db_charset,
            'collation' => 'utf8_general_ci',
            'prefix' => '',
        ]);

// Set the event dispatcher used by Eloquent models... (optional)

        $capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
    }

    public function __get($name)
    {
        if (isset(self::$objects[$name])) {
            return(self::$objects[$name]);
        }

        if (class_exists(ucfirst($name))) {
            $class = ucfirst($name);
        } elseif (array_key_exists($name, $this->classes)) {
            $class = $this->classes[$name];
        } else {
            return null;
        }

        self::$objects[$name] = new $class();

        return self::$objects[$name];
    }

    public function dd($item)
    {
        foreach (func_get_args() as $x) {
            dump($x);
        }
        die;
    }
}
