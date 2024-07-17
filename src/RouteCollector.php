<?php

namespace Awesomeundead\Undeadstore;

class RouteCollector
{
    public $routes;

    public function get($path, $callback)
    {
        $this->routes[] = ['GET', $path, $callback];
    }

    public function post($path, $callback)
    {
        $this->routes[] = ['POST', $path, $callback];
    }
}