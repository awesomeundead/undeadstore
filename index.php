<?php

use Awesomeundead\Undeadstore\App;
use Awesomeundead\Undeadstore\HttpException;

date_default_timezone_set('America/Sao_Paulo');

$host = $_SERVER['HTTP_HOST'];
$protocol = !empty($_SERVER['HTTPS']) ? 'https' : 'http';

define('HOST', "{$protocol}://{$host}");
define('ROOT', __DIR__);
define('VIEW', ROOT . '/template/');
define('BASE_PATH', '');
define('URL_PATH', substr_replace(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '', 0, strlen(BASE_PATH)));

function redirect($path = '/')
{
    header('Location: ' . HOST . BASE_PATH . $path);
    exit;
}

try
{
    $app = App::create();
    $app->basePath(BASE_PATH);
    
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
        echo $e->getMessage();
    }
}