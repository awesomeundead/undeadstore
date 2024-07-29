<?php

$cookies = require 'trade_config.php';

$sessionid = $cookies['sessionid'];
$steamLoginSecure = $cookies['steamLoginSecure'];

if (preg_match('#^https://steamcommunity.com/tradeoffer/new/\?partner=(\d+)&token=(\w+)$#', $steam_trade_url, $matches))
{
    $steamID3 = $matches[1];
    $token = $matches[2];
}

$tradeoffer = [
    'newversion' => true,
    'version' => 4,
    'me' => [
        'assets' => $assets,
        'currency' => [],
        'ready' => false
    ],
    'them' => [
        'assets' => [
            [
                'appid' => '',
                'contextid' => '',
                'amount' => '',
                'assetid' => ''
            ]
        ],
        'currency' => [],
        'ready' => false
    ]
];

$trade_offer_create_params = [
    'trade_offer_access_token' => $token
];

$headers = [
    "Referer: https://steamcommunity.com/tradeoffer/new/?partner={$steamID3}&token={$token}"
];

$body = [
    'sessionid' => $sessionid,
    'serverid' => '1',
    'partner' => $steamID64,
    'tradeoffermessage' => 'Agradecemos a sua compra na Undead Store',
    'json_tradeoffer' => json_encode($tradeoffer),
    'captcha' => '',
    'trade_offer_create_params' => json_encode($trade_offer_create_params),
    'tradeofferid_countered' => ''
];

$options = [
    CURLOPT_URL => 'https://steamcommunity.com/tradeoffer/new/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_COOKIE => "sessionid={$sessionid}; steamLoginSecure={$steamLoginSecure}"
];

$curl = curl_init();

curl_setopt_array($curl, $options);

$response = curl_exec($curl);
$info = curl_getinfo($curl);

file_put_contents(ROOT . '/log/trade_' . $info['http_code'] . '_' . $purchase_id . '_' . time() . '.json', $response);