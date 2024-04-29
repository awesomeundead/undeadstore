<?php

date_default_timezone_set('America/Sao_Paulo');
session_set_cookie_params(['httponly' => true]);

$host = $_SERVER['HTTP_HOST'];
$protocol = !empty($_SERVER['HTTPS']) ? 'https' : 'http';

define('HOST', "{$protocol}://{$host}");
define('ROOT', __DIR__);
define('SRC', ROOT . '/src/');
define('VIEW', ROOT . '/template/');
define('BASE_PATH', '');
define('URL_PATH', substr_replace(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '', 0, strlen(BASE_PATH)));

function redirect($path = '/')
{
    header('Location: ' . HOST . BASE_PATH . $path);
    exit;
}

require ROOT . '/include/app_factory.php';

$app = AppFactory::create();

$routes = require ROOT . '/routes.php';
$routes($app);

$app->run();