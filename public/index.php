<?php

if (php_sapi_name() == 'cli-server')
{
    if (preg_match('/\.(?:png|jpg|jpeg|gif|svg|css|js)$/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)))
    {
        return false;
    }
}

date_default_timezone_set('America/Sao_Paulo');

$protocol = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
define('HOST', "{$protocol}://{$_SERVER['HTTP_HOST']}");
define('BASE_PATH', '');
define('ROOT', dirname(__DIR__));
define('VIEW', ROOT . '/template/');

function redirect($path = '/')
{
    header('Location: ' . HOST . BASE_PATH . $path);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../index.php';