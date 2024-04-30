<?php

function steam_openid_init($params)
{
    $login_url_params = [
        'openid.ns'         => 'http://specs.openid.net/auth/2.0',
        'openid.mode'       => 'checkid_setup',
        'openid.return_to'  => $params['openid.return_to'],
        'openid.realm'      => $params['openid.realm'],
        'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
        'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
    ];

    $steam_login_url = 'https://steamcommunity.com/openid/login' . '?' . http_build_query($login_url_params, '', '&');

    header("location: $steam_login_url");
    exit;
}

function steam_openid_process($steam_api_key)
{
    $params = [
        'openid.assoc_handle' => $_GET['openid_assoc_handle'],
        'openid.signed'       => $_GET['openid_signed'],
        'openid.sig'          => $_GET['openid_sig'],
        'openid.ns'           => 'http://specs.openid.net/auth/2.0',
        'openid.mode'         => 'check_authentication',
    ];
    
    $signed = explode(',', $_GET['openid_signed']);
        
    foreach ($signed as $item)
    {
        $val = $_GET['openid_' . str_replace('.', '_', $item)];
        $params['openid.' . $item] = stripslashes($val);
    }
    
    $data = http_build_query($params);
    $context = stream_context_create(
    [
        'http' =>
        [
            'method' => 'POST',
            'header' => "Accept-language: en\r\n" .
                        "Content-type: application/x-www-form-urlencoded\r\n" .
                        'Content-Length: ' . strlen($data) . "\r\n",
            'content' => $data,
        ],
    ]);
    
    $result = file_get_contents('https://steamcommunity.com/openid/login', false, $context);
    
    if (preg_match("#is_valid\s*:\s*true#i", $result))
    {
        preg_match('#^https://steamcommunity.com/openid/id/([0-9]{17,25})#', $_GET['openid_claimed_id'], $matches);
        $steamID64 = is_numeric($matches[1]) ? $matches[1] : 0;
    }
    else
    {
        return false;
    }
    
    $response = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$steam_api_key}&steamids={$steamID64}");
    return json_decode($response, true);
}