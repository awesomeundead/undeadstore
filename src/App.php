<?php

namespace Awesomeundead\Undeadstore;

use Awesomeundead\Undeadstore\HttpException;
use DI\Container;

class App extends RouteCollector
{
    private static $app;

    private static $basePath;

    private static $container;

    private static $groups;

    private function __construct(){}
    
    public static function create()
    {
        if(!isset(self::$app))
        {
            self::$app = new App();
        }

        return self::$app;
    }

    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }

    public function basePath($path)
    {
        self::$basePath = $path;
    }

    public function group($path, $callback)
    {
        self::$groups[] = [$path, $callback];
    }

    public function run()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r)
        {
            if (!empty(self::$groups))
            {
                foreach (self::$groups as $group)
                {
                    list($path, $callback) = $group;

                    $collection = new RouteCollector;

                    call_user_func($callback, $collection);

                    $r->addGroup($path, function (\FastRoute\RouteCollector $r) use ($collection)
                    {                        
                        foreach ($collection->routes as $route)
                        {
                            $r->addRoute(...$route);
                        }
                    });
                }
            }

            foreach ($this->routes as $route)
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
                    [$controller, $method] = $handler;

                    $controller = self::$container->get($controller);

                    call_user_func_array([$controller, $method], $vars);
                }
                
                break;
        }
    }
}