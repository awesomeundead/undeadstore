<?php

use Awesomeundead\Undeadstore\App;
use Awesomeundead\Undeadstore\Session;

return function (App $app)
{
    $app->get('/', function ()
    {
        $session = Session::create();
        $content_view = 'index.phtml';

        require VIEW . 'layout.phtml';
    });
    
    $app->get('/auth', function ()
    {
        require ROOT . '/app/auth.php';
    });

    $app->get('/auth/login', function ()
    {
        require ROOT . '/app/auth_login.php';
    });

    $app->get('/cs2', function ()
    {
        redirect('/');
    });

    $app->get('/cs2/', function ()
    {
        redirect('/');
    });

    $app->get('/skins', function ()
    {
        redirect('/');
    });

    $app->get('/skins/', function ()
    {
        redirect('/');
    });

    $app->get('/cart', function ()
    {
        $session = Session::create();

        require ROOT . '/app/cart.php';
    });

    $app->get('/cart/add', function ()
    {
        $session = Session::create();

        $item_id = $_GET['item_id'] ?? false;

        if ($item_id)
        {
            require ROOT . '/include/pdo.php';

            $query = 'SELECT * FROM items WHERE id = :id AND availability = 1';
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $item_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result)
            {
                if ($result['type_name'] == 'agent')
                {
                    $query = 'SELECT * FROM agents WHERE id = :id';
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['id' => $result['type_id']]);
                    $item = $stmt->fetch(PDO::FETCH_ASSOC);

                    $result['full_name'] = "{$item['agent_name']} | {$item['agent_category']}";
                    $result['full_name_br'] = "{$item['agent_name_br']} | {$item['agent_category_br']}";
                    $result['image'] = $item['image'];
                }
                elseif ($result['type_name'] == 'weapon')
                {
                    $query = 'SELECT * FROM weapons WHERE id = :id';
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['id' => $result['type_id']]);
                    $item = $stmt->fetch(PDO::FETCH_ASSOC);

                    $exterior = [
                        'fn' => 'Factory New',
                        'mw' => 'Minimal Wear',
                        'ft' => 'Field-Tested',
                        'ww' => 'Well Worm',
                        'bs' => 'Battle-Scarred'
                    ];
                    
                    $exterior_br = [
                        'fn' => 'Nova de Fábrica',
                        'mw' => 'Pouca Usada',
                        'ft' => 'Testada em Campo',
                        'ww' => 'Bem Desgastada',
                        'bs' => 'Veterana de Guerra'
                    ];

                    $stattrak = $item['weapon_stattrak'] ? 'StatTrak' : '';
                    $result['full_name'] = implode(' ', [$item['weapon_type'], $stattrak, $item['weapon_name'], $exterior[$item['weapon_exterior']]]);
                    $result['full_name_br'] = implode(' ', [$item['weapon_type_br'], $stattrak, $item['weapon_name_br'], $exterior_br[$item['weapon_exterior']]]);
                    $result['image'] = "{$item['image']}_{$item['weapon_exterior']}";
                }

                $cart_items = $session->get('cart_items');
                $cart_items[$result['id']] = $result;
                $session->set('cart_items', $cart_items);
            }
        }

        require ROOT . '/app/cart.php';
    });

    $app->get('/cart/delete', function ()
    {
        $session = Session::create();

        $item_id = $_GET['item_id'] ?? false;

        if ($item_id)
        {
            $cart_items = $session->get('cart_items');
            unset($cart_items[$item_id]);
            $session->set('cart_items', $cart_items);
        }

        require ROOT . '/app/cart.php';
    });

    $app->post('/cart/coupon', function ()
    {
        $session = Session::create();

        $coupon = $_POST['coupon'] ?? false;

        if ($coupon)
        {
            require ROOT . '/include/pdo.php';

            $coupon = strtoupper(trim($coupon));

            $query = 'SELECT * FROM coupon WHERE name = :name';
            $stmt = $pdo->prepare($query);
            $stmt->execute(['name' => $coupon]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result)
            {
                $user_id = $session->get('user_id');

                if (is_null($result['user_id']))
                {
                    $session->set('cart_coupon', $result);
                    $session->flash('coupon', 'Cupom adicionado.');
                    redirect('/cart');
                }

                if ($result['user_id'] == $user_id)
                {
                    $session->set('cart_coupon', $result);
                    $session->flash('coupon', 'Cupom adicionado.');
                    redirect('/cart');
                }
            }
        }

        $session->remove('cart_coupon');
        $session->flash('coupon', 'Cupom inválido.');
        redirect('/cart');
    });

    $app->get('/checkout', function ()
    {
        require ROOT . '/app/checkout.php';
    });

    $app->get('/checkout/end', function ()
    {
        require ROOT . '/app/checkout_end.php';
    });

    $app->post('/checkout/trade', function ()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $steam_trade_url = $_POST['steam_trade_url'] ?? false;
        $steam_trade_url = trim($steam_trade_url);

        if (preg_match('#^https://steamcommunity.com/tradeoffer/new/\?partner=(\d+)&token=(\w+)$#', $steam_trade_url, $matches))
        {
            require ROOT . '/include/pdo.php';

            $query = 'UPDATE users SET steam_trade_url = :steam_trade_url WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $params = [
                'id' => $session->get('user_id'),
                'steam_trade_url' => $_POST['steam_trade_url']
            ];
            $stmt->execute($params);
            $session->flash('trade', 'Atualizado');
        }
        else
        {
            $session->flash('trade', 'URL inválida');
        }
        
        redirect('/checkout');
    });

    $app->get('/data', function ()
    {
        require ROOT . '/include/pdo.php';

        $query = 'SELECT *, items.id,
        (CASE 
            WHEN items.type_name = "agent" THEN agents.image
            WHEN items.type_name = "weapon" THEN weapons.image
        END) AS image
        FROM items
        LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
        LEFT JOIN weapons ON items.type_id = weapons.id AND items.type_name = "weapon"
        WHERE availability = :availability';
        $params = ['availability' => 1];

        $item = $_GET['item'] ?? false;

        if ($item)
        {
            $parts = explode(':', $item);

            if ($parts[0] == 'agent')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
                WHERE items.type_name = :agent';
                $params = ['agent' => 'agent'];
            }
            elseif ($parts[0] == 'weapon')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN weapons ON items.type_id = weapons.id AND items.type_name = "weapon"
                WHERE weapons.weapon_type = :weapon';
                $params = ['weapon' => $parts[1]];
            }
        }
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($list);
    });

    $app->post('/input/mercadopago', function ()
    {
        $data_id = $_REQUEST['data_id'] ?? false;
        $xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        $xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';
        
        if (preg_match('/^ts=(?P<ts>\d+),v1=(?P<hash>\w{64})$/', $xSignature, $matches))
        {
            require ROOT . '/include/pdo.php';

            $query = 'INSERT INTO mercadopago (data_id, ts, hash) VALUES (:data_id, :ts, :hash)';
            $stmt = $pdo->prepare($query);
            $params = [
                'data_id' => $data_id,
                'ts' => $matches['ts'],
                'hash' => $matches['hash']
            ];
            $stmt->execute($params);

            $config = (require ROOT . '/config.php')['mercadopago'];
            $access_token = $config['access_token'];
            $secret = $config['secret_signature'];

            $manifest = "id:{$data_id};request-id:{$xRequestId};ts:{$matches['ts']};";
            $sha = hash_hmac('sha256', $manifest, $secret);

            if ($sha === $matches['hash'])
            {
                $context = stream_context_create([
                    'http' => [
                        'header' => 'Authorization: Bearer ' . $access_token
                    ],
                ]);
                
                $response = file_get_contents('https://api.mercadopago.com/v1/payments/' . $data_id, false, $context);
                $data = json_decode($response, true);

                if ($data['status'] != 'approved')
                {
                    exit;
                }

                preg_match('/^US(?P<id>\w{7})$/', $data['external_reference'], $matches);
                $purchase_id = base_convert(strtolower($matches['id']), 36, 10);

                $query = 'UPDATE purchase SET status = :status WHERE id = :id';
                $stmt = $pdo->prepare($query);
                $params = [
                    'id' => $purchase_id,
                    'status' => 'Pagamento aprovado'
                ];
                $stmt->execute($params);
            }
        }
    });

    $app->get('/logout', function ()
    {
        session_start();
        session_unset();
        session_destroy();
        
        redirect();
    });

    $app->get('/orders', function ()
    {
        require ROOT . '/app/orders.php';
    });

    $app->get('/partners', function ()
    {
        $session = Session::create();

        $content_view = 'partners.phtml';
        $settings_title = 'Parceiros';

        require VIEW . 'layout.phtml';
    });

    $app->get('/pay', function ()
    {
        require ROOT . '/app/pay.php';
    });
    
    /*
    $app->get('/pay/success', function ()
    {
        $collection_id = $_GET['collection_id'];
        $access_token = 'TEST-7407069493848525-043015-b6eef5c68c01daf9e227f8ef3727ae45-234415597';

        $context = stream_context_create(
        [
            'http' =>
            [
                'method' => 'GET',
                'header' => 'Authorization: Bearer ' . $access_token
            ],
        ]);
        
        $response = file_get_contents('https://api.mercadopago.com/v1/payments/' . $collection_id, false, $context);
        $data = json_decode($response, true);

        print_r($data);
    });
    */

    $app->get('/payment/success', function ()
    {
        $session = Session::create();
        $session->flash('payment', 'Pagamento concluído com sucesso.');

        redirect('/orders');
    });

    $app->get('/payment/failure', function ()
    {
        $purchase_id = $_GET['purchase_id'] ?? false;

        $session = Session::create();
        $session->flash('payment', 'Falha no pagamento, tente novamente.');

        redirect("/pay?purchase_id={$purchase_id}");
    });


    $app->get('/qrcode', function ()
    {
        $data = $_GET['data'] ?? false;

        if ($data)
        {
            require ROOT . '/include/qrcode.php';

            $generator = new QRCode(urldecode($data), ['w' => 400, 'h' => 400, 'wq' => 0]);
            $generator->output_image();
            $image = $generator->render_image();
            imagepng($image);
            imagedestroy($image);
        }
    });

    $app->get('/settings', function ()
    {
        require ROOT . '/app/settings.php';
    });

    $app->post('/settings', function ()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        require ROOT . '/include/pdo.php';

        $steam_trade_url = $_POST['steam_trade_url'] ?? false;
        $steam_trade_url = trim($steam_trade_url);

        if (!preg_match('#^https://steamcommunity.com/tradeoffer/new/\?partner=(\d+)&token=(\w+)$#', $steam_trade_url, $matches))
        {
            $session->flash('settings', 'URL inválida.');
            redirect('/settings');
        }

        $query = 'UPDATE users SET steam_trade_url = :steam_trade_url, name = :name, email = :email, phone = :phone WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $params = [
            'id' => $session->get('user_id'),
            'steam_trade_url' => $steam_trade_url,
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        ];
        $result = $stmt->execute($params);

        if ($result)
        {
            $session->flash('settings', 'Atualizado com sucesso.');
        }
        else
        {
            $session->flash('settings', 'Ocorreu um erro ao atualizar.');
        }

        redirect('/settings');
    });

    $app->get('/support', function ()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $content_view = 'support.phtml';

        require VIEW . 'layout.phtml';
    });

    $app->post('/support', function ()
    {

    });
};