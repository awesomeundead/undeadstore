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
            if (isset($_COOKIE['login']))
            {
                $token = htmlspecialchars($_COOKIE['login']);

                $parts = explode(':', $token);

                if ($parts && count($parts) == 2)
                {
                    [$selector, $validator] = $parts;
                }

                $pdo = Database::connect();

                $query = 'SELECT * FROM login_log WHERE selector = :selector';
                $stmt = $pdo->prepare($query);
                $stmt->execute(['selector' => $selector]);
                $login = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (password_verify($validator, $login['hashed_validator']))
                {
                    if ($login['expire_date'] > date('Y-m-d H:i:s'))
                    {
                        $query = 'SELECT * FROM users WHERE id = :id';
                        $stmt = $pdo->prepare($query);
                        $stmt->execute(['id' => $login['user_id']]);
                        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                        $steam_api_key = (require ROOT . '/config.php')['steam_api_key'];
                        $steamID64 = $user['steamid'];

                        $response = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$steam_api_key}&steamids={$steamID64}");
                        $response = json_decode($response, true);

                        $userData = $response['response']['players'][0];
                        $session->set('logged_in', true);
                        $session->set('user_id', $login['user_id']);
                        $session->set('steamid', $userData['steamid']);
                        $session->set('steam_name', $userData['personaname']);
                        $session->set('steam_avatar', $userData['avatar']);                       

                        $query = 'UPDATE users SET personaname = :personaname, avatarhash = :avatarhash WHERE id = :id';
                        $stmt = $pdo->prepare($query);
                        $params = [
                            'id' => $session->get('user_id'),
                            'personaname' => $userData['personaname'],
                            'avatarhash' => $userData['avatarhash']
                        ];
                        $stmt->execute($params);

                        if (isset($_GET['redirect']))
                        {
                            redirect('/' . $_GET['redirect']);
                        }

                        redirect();
                    }
                }
            }

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

                $query = 'UPDATE users SET personaname = :personaname, avatarhash = :avatarhash WHERE id = :id';
                $stmt = $pdo->prepare($query);
                $params = [
                    'id' => $session->get('user_id'),
                    'personaname' => $userData['personaname'],
                    'avatarhash' => $userData['avatarhash']
                ];
                $result = $stmt->execute($params);
            }
            else
            {
                // NOVO REGISTRO DE USUÁRIO
                $query = 'INSERT INTO users (steamid, personaname, avatarhash, created_date) VALUES (:steamid, :personaname, :avatarhash, :created_date)';
                $stmt = $pdo->prepare($query);
                $params = [
                    'steamid' => $session->get('steamid'),
                    'personaname' => $userData['personaname'],
                    'avatarhash' => $userData['avatarhash'],
                    'created_date' => date('Y-m-d')
                ];
                $result = $stmt->execute($params);

                if ($result)
                {
                    $user_id = $pdo->lastInsertId();

                    $session->set('user_id', $user_id);

                    $query = 'INSERT INTO wallet (id, balance, pending) VALUES (:id, :balance, :pending)';
                    $stmt = $pdo->prepare($query);
                    $params = [
                        'id' => $user_id,
                        'balance' => 0,
                        'pending' => 0
                    ];
                    $result = $stmt->execute($params);

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

            $selector = bin2hex(random_bytes(16));
            $validator = bin2hex(random_bytes(32));
            $token = $selector . ':' . $validator;

            $hashed_validator = password_hash($validator, PASSWORD_DEFAULT);
            $expire_date = strtotime('+1 month');

            $address = $_SERVER['HTTP_CLIENT_IP']
                    ?? $_SERVER['HTTP_X_FORWARDED_FOR']
                    ?? $_SERVER['HTTP_X_FORWARDED']
                    ?? $_SERVER['HTTP_FORWARDED_FOR']
                    ?? $_SERVER['HTTP_FORWARDED']
                    ?? $_SERVER['REMOTE_ADDR']
                    ?? 'UNKNOWN';

            // NOVO REGISTRO DE LOGIN
            $query = 'INSERT INTO login_log (user_id, selector, hashed_validator, login_date, expire_date, user_ip)
                      VALUES (:user_id, :selector, :hashed_validator, :login_date, :expire_date, :user_ip)';
            $stmt = $pdo->prepare($query);
            $params = [
                'user_id' => $session->get('user_id'),
                'selector' => $selector,
                'hashed_validator' => $hashed_validator,
                'login_date' => date('Y-m-d H:i:s'),
                'expire_date' => date('Y-m-d H:i:s', $expire_date),
                'user_ip' => $address
            ];
            $result = $stmt->execute($params);

            if ($result)
            {
                setcookie('login', $token, ['expires' => $expire_date, 'path' => '/', 'httponly' => true]);
            }

            if (isset($_GET['redirect']))
            {
                redirect('/' . $_GET['redirect']);
            }
        }

        redirect();
    }
}