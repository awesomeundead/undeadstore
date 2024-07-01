<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Auth
{
    public function index()
    {
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
    }

    public function login()
    {
        $session = Session::create();

        if (!$session->get('logged_in'))
        {
            $steam_api_key = (require ROOT . '/config.php')['steam_api_key'];
                    
            // RETORNO DO STEAM
            // VERIFICA SE O USUÁRIO EXISTE NO STEAM
            require ROOT . '/include/steam_openid.php';

            $response = steam_openid_process($steam_api_key);

            if (!$response)
            {
                redirect('/?err=auth');
            }

            $userData = $response['response']['players'][0];
            $session->set('logged_in', true);
            $session->set('steamid', $userData['steamid']);
            $session->set('steam_name', $userData['personaname']);
            $session->set('steam_avatar', $userData['avatar']);

            $pdo = Database::connect();

            // VERIFICA SE ESSE USUÁRIO JÁ É REGISTRADO
            $query = 'SELECT id, verified_email FROM users WHERE steamid = :steamid';
            $stmt = $pdo->prepare($query);
            $stmt->execute(['steamid' => $session->get('steamid')]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result)
            {
                // USUÁRIO JÁ REGISTRADO
                $session->set('user_id', $result['id']);

                if ($result['verified_email'] == '1')
                {
                    $verified_email = true;
                }
            }
            else
            {
                // NOVO REGISTRO DE USUÁRIO
                $query = 'INSERT INTO users (steamid, created_date) VALUES (:steamid, :created_date)';
                $stmt = $pdo->prepare($query);
                $params = [
                    'steamid' => $session->get('steamid'),
                    'created_date' => date('Y-m-d')
                ];
                $result = $stmt->execute($params);

                if ($result)
                {
                    $session->set('user_id', $pdo->lastInsertId());

                    require ROOT . '/include/mail.php';

                    $steamid = $session->get('steamid');
                    $personaname = $session->get('steam_name');
                    $params['subject'] = 'Novo usuário registrado';
                    $params['message'] = "O usuário {$personaname} ({$steamid}), foi registrado com sucesso.";
                    
                    send_mail($params);
                }
            }

            if (!isset($verified_email))
            {
                $session->set('notification', 'EMAIL_ADDRESS_NOT_VERIFIED');
            }

            $address = $_SERVER['HTTP_CLIENT_IP']
                    ?? $_SERVER['HTTP_X_FORWARDED_FOR']
                    ?? $_SERVER['HTTP_X_FORWARDED']
                    ?? $_SERVER['HTTP_FORWARDED_FOR']
                    ?? $_SERVER['HTTP_FORWARDED']
                    ?? $_SERVER['REMOTE_ADDR']
                    ?? 'UNKNOWN';

            // NOVO REGISTRO DE LOGIN
            $query = 'INSERT INTO login_log (user_id, login_date, user_ip) VALUES (:user_id, :login_date, :user_ip)';
            $stmt = $pdo->prepare($query);
            $params = [
                'user_id' => $session->get('user_id'),
                'login_date' => date('Y-m-d H:i:s'),
                'user_ip' => $address
            ];
            $stmt->execute($params);

            if (isset($_GET['redirect']))
            {
                redirect('/' . $_GET['redirect']);
            }
        }

        redirect();
    }
}