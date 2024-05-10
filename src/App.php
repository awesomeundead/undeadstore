<?php

namespace Awesomeundead\Undeadstore;

use Awesomeundead\Undeadstore\HttpException;

class App
{
    private static $app;

    private static $basePath;

    private static $routes;

    private function __construct(){}
    
    public static function create()
    {
        if(!isset(self::$app))
        {
            self::$app = new App();
        }

        return self::$app;
    }

    public function basePath($path)
    {
        self::$basePath = $path;
    }

    public function get($path, $callback)
    {
        //self::$routes['get'][$path] = $callback;
        self::$routes[] = ['GET', $path, $callback];
    }

    public function post($path, $callback)
    {
        //self::$routes['post'][$path] = $callback;
        self::$routes[] = ['POST', $path, $callback];
    }

    public function run()
    {
        /*
        $base_path = self::$basePath ?? '';
        $url_path = substr_replace(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '', 0, strlen($base_path));
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $callback = self::$routes[$method][$url_path] ?? false;

        if (is_callable($callback))
        {
            call_user_func($callback);
            exit;
        }
        
        throw new HttpException('NOT FOUND', 404);
        //throw new HttpException('METHOD NOT ALLOWED', 405);
        */

        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r)
        {
            /*
            if (!empty($this->group))
            {
                $this->group_routes($r);
            }
            */

            foreach (self::$routes as $route)
            {
                $r->addRoute(...$route);
            }
        });

        $http_method = $_SERVER['REQUEST_METHOD'];
        $path = substr_replace(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '', 0, strlen(self::$basePath ?? ''));

        $route_info = $dispatcher->dispatch($http_method, $path);

        switch ($route_info[0])
        {
            case \FastRoute\Dispatcher::NOT_FOUND:
                throw new HttpException('NOT FOUND', 404);
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                throw new HttpException('METHOD NOT ALLOWED', 405);
                break;
            case \FastRoute\Dispatcher::FOUND:
                [,$handler, $vars] = $route_info;
                
                if ($handler instanceof \Closure)
                {
                    call_user_func($handler, $vars);
                }
                else
                {
                    [$controller,$method] = $handler;
                    call_user_func_array([new $controller, $method], $vars);
                }
                
                break;
        }
    }
}