<?php

return function ($app)
{
    $app->get('/', 'index.php');

    $app->get('/auth', 'auth.php');

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
        session_start();

        require SRC . '/cart.php';
    });

    $app->get('/cart/add', function ()
    {
        session_start();

        require ROOT . '/include/pdo.php';

        $item_id = $_GET['item_id'] ?? false;

        if ($item_id)
        {
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

                $_SESSION['cart']['items'][$result['id']] = $result;
            }
        }

        require SRC . '/cart.php';
    });

    $app->get('/cart/delete', function ()
    {
        session_start();

        $item_id = $_GET['item_id'] ?? false;

        if ($item_id)
        {
            unset($_SESSION['cart']['items'][$item_id]);
        }

        require SRC . '/cart.php';
    });

    $app->post('/cart/coupon', function ()
    {
        session_start();

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
                $user_id = $_SESSION['user']['id'] ?? false;

                if (is_null($result['user_id']))
                {
                    $_SESSION['cart']['coupon'] = $result;
                    redirect('/cart');
                }

                if ($result['user_id'] == $user_id)
                {
                    $_SESSION['cart']['coupon'] = $result;
                    redirect('/cart');
                }
            }
        }

        $_SESSION['cart']['coupon'] = false;
        $_SESSION['__flash'] = 'Cupom inválido.';
        redirect('/cart');
    });

    $app->get('/checkout/end', 'checkout_end.php');

    $app->get('/checkout', 'checkout.php');

    $app->post('/checkout/trade', function ()
    {
        session_start();

        $logged_in = $_SESSION['logged_in'] ?? false;

        if (!$logged_in)
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
                'id' => $_SESSION['user']['id'],
                'steam_trade_url' => $_POST['steam_trade_url']
            ];
            $stmt->execute($params);

            $_SESSION['__flash'] = 'Atualizado';
        }
        else
        {
            $_SESSION['__flash'] = 'URL inválida';
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

    $app->get('/logout', function ()
    {
        session_start();
        session_destroy();
        
        redirect();
    });

    $app->get('/orders', 'orders.php');

    $app->get('/settings', 'settings.php');

    $app->post('/settings', function ()
    {
        session_start();

        $logged_in = $_SESSION['logged_in'] ?? false;

        if (!$logged_in)
        {
            redirect('/auth');
        }

        require ROOT . '/include/pdo.php';

        $query = 'UPDATE users SET steam_trade_url = :steam_trade_url, name = :name, email = :email, phone = :phone WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $params = [
            'id' => $_SESSION['user']['id'],
            'steam_trade_url' => $_POST['steam_trade_url'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        ];
        $result = $stmt->execute($params);

        if ($result)
        {
            redirect('/settings');
        }
    });

    /*
    $app->get('/partners', function ()
    {
        session_start();

        $logged_in = $_SESSION['logged_in'] ?? false;

        if ($logged_in)
        {
            $steamid = $_SESSION['user']['steamid'];
            $steam_name = $_SESSION['user']['personaname'];
            $steam_avatar = $_SESSION['user']['avatar'];
        }

        $content_view = 'partners.phtml';
        $settings_title = 'Parceiros';

        require VIEW . 'layout.phtml';
    });
    */

    $app->get('/pay', 'pay.php');

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
};