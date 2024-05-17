<?php

use Awesomeundead\Undeadstore\App;
use Awesomeundead\Undeadstore\HttpException;
use DI\ContainerBuilder;

try
{
    $builder = new ContainerBuilder();
    $container = $builder->build();

    App::setContainer($container);
    $app = App::create();
    //$app->basePath(BASE_PATH);
    
    $routes = require ROOT . '/routes.php';
    $routes($app);
    
    $app->run();
}
catch (HttpException $e)
{
    if ($e->getCode() == 404)
    {
        require ROOT . '/404.html';
    }
}
catch (Throwable $e)
{
    //PDOException
    //MPApiException
    $errors = (require ROOT . '/config.php')['errors'] ?? false;

    if ($errors)
    {
        $message =  $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();

        echo "{$message} file: {$file} line: {$line}";
    }
}