<?php

$login_url_params = [
    'openid.ns'         => 'http://specs.openid.net/auth/2.0',
    'openid.mode'       => 'checkid_setup',
    'openid.return_to'  => HOST . BASE_PATH . '/auth',
    'openid.realm'      => HOST,
    'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
    'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
];

if (isset($_GET['redirect']))
{
    $login_url_params['openid.return_to'] .= '?redirect=' . $_GET['redirect'];
}

$steam_login_url = 'https://steamcommunity.com/openid/login' . '?' . http_build_query($login_url_params, '', '&');

header("location: $steam_login_url");
exit;