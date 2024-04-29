<?php

class AppFactory
{
    private static $app;

    private static $routes;

    public static function create()
    {
        if(!isset(self::$app))
        {
            self::$app = new AppFactory();
        }

        return self::$app;
    }

    public function get($path, $callback)
    {
        self::$routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        self::$routes['post'][$path] = $callback;
    }

    public function run()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $callback = self::$routes[$method][URL_PATH] ?? false;

        if ($callback)
        {
            if (is_callable($callback))
            {
                call_user_func($callback);
                exit;
            }
            elseif (is_string($callback) && file_exists(SRC . "/{$callback}"))
            {
                require SRC . "/{$callback}";
                exit;
            }
        }
        
        echo '404';
    }

    private function __construct(){}
}