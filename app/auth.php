<?php

use Awesomeundead\Undeadstore\Session;

$session = Session::create();

if (!$session->get('logged_in'))
{
    require ROOT . '/include/steam_openid.php';

    $params = [
        'openid.return_to'  => HOST . BASE_PATH . '/auth/login',
        'openid.realm'      => HOST,
    ];

    if (isset($_GET['redirect']))
    {
        $params['openid.return_to'] .= '?redirect=' . $_GET['redirect'];
    }

    steam_openid_init($params);
}

redirect();