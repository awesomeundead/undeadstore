<?php

function request()
{
    $options = [
        CURLOPT_URL => 'https://steamcommunity.com/login/home/',
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        CURLOPT_RETURNTRANSFER => true
    ];

    $saved_cookies = require __DIR__ . '/trade_config.php';

    foreach ($saved_cookies as $name => $value)
    {
        if ($name == 'browserid' || $name == 'steamLoginSecure' || $name == 'timezoneOffset')
        {
            $cookies[] = "{$name}={$value}";
        }
    }

    if ($cookies ?? false)
    {
        $options[CURLOPT_COOKIE] = implode('; ', $cookies);
    }

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);

    $response = substr($response, 0, strpos($response, "\r\n\r\n"));
    $headers = explode("\r\n", $response);

    foreach ($headers as $item)
    {
        if (preg_match('/^Set-Cookie:\s*([^;]*)/i', $item, $matches))
        {
            list($name, $value) = explode('=', $matches[1]);

            $saved_cookies[$name] = $value;
        }
    }

    if ($saved_cookies ?? false)
    {
        $export = var_export($saved_cookies, true);

        file_put_contents(__DIR__ . '/trade_config.php', "<?php return {$export};");
    }
}

request();