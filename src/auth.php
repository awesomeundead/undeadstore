<?php

session_start();

$logged_in = $_SESSION['logged_in'] ?? false;

if (!$logged_in)
{
    if (isset($_GET['openid_assoc_handle'], $_GET['openid_claimed_id'], $_GET['openid_sig'], $_GET['openid_signed']))
    {
        $steam_api_key = (require ROOT . '/config.php')['steam_api_key'];
        
        // RETORNO DO STEAM
        // VERIFICA SE O USUÁRIO EXISTE NO STEAM
        require ROOT . '/include/openid_process.php';

        $userData = $response['response']['players'][0];

        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = 
        [
            'steamid' => $userData['steamid'],
            'personaname' => $userData['personaname'],
            'avatar' => $userData['avatar']
        ];

        require ROOT . '/include/login.php';

        if (isset($_GET['redirect']))
        {
            redirect('/' . $_GET['redirect']);
        }
    }
    else
    {
        // REDIRECIONA PARA A PAGÍNA DO STEAM
        require ROOT . '/include/openid_init.php';
    }
}

redirect();