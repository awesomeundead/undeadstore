<?php

if (php_sapi_name() == 'cli-server')
{
    if (preg_match('/\.(?:png|jpg|jpeg|gif|svg|css|js)$/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)))
    {
        return false;
    }
}

require __DIR__ . '/../app.php';