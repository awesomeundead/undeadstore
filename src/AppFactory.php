<?php

namespace Awesomeundead\Undeadstore;

class AppFactory
{
    private static $app;

    private static $basePath;

    private static $routes;

    public static function create()
    {
        if(!isset(self::$app))
        {
            self::$app = new AppFactory();
        }

        return self::$app;
    }

    public function basePath($path)
    {
        self::$basePath = $path;
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
        $base_path = self::$basePath ?? '';
        $url_path = substr_replace(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '', 0, strlen($base_path));
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $callback = self::$routes[$method][$url_path] ?? false;

        if (is_callable($callback))
        {
            call_user_func($callback);
            exit;
        }
        
        echo '404';
    }

    private function __construct(){}
}