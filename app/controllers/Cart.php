<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Cart extends Controller
{
    public function index()
    {
        $session = Session::create();
        
        $cart = $session->get('cart');

        if ($cart['items'] ?? false)
        {
            $count = count($cart['items']);

            foreach ($cart['items'] as $index => $item)
            {
                $prices[] = $item['offer_price'] ?? $item['price'];
            }
            
            $cart['subtotal'] = array_sum($prices);
            $cart['discount'] = 0;
            $cart['percent'] = 0;
            
            if ($cart['coupon'] ?? false)
            {
                $coupon_name = $cart['coupon']['name'];
                $cart['discount'] = $cart['subtotal'] / 100 * $cart['coupon']['percent'];
                $cart['percent'] = $cart['coupon']['percent'];
            }

            $cart['total'] = $cart['subtotal'] - $cart['discount'];

            $session->set('cart', $cart);
        }

        echo $this->templates->render('cart/index', [
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name')
            ],
            'cart' => $cart,
            'notification' => $session->flash('coupon')
        ]);
    }

    public function add()
    {
        $session = Session::create();

        $item_id = $_GET['item_id'] ?? false;

        if ($item_id)
        {
            $pdo = Database::connect();

            $query = 'SELECT *,
            IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price
            FROM items WHERE id = :id AND availability = 1';
            
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $item_id]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result)
            {
                if ($result['type_name'] == 'agent')
                {
                    $query = 'SELECT * FROM agents WHERE id = :id';
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['id' => $result['type_id']]);
                    $item = $stmt->fetch(\PDO::FETCH_ASSOC);

                    $result['full_name'] = "{$item['agent_name']} | {$item['agent_family']}";
                    $result['full_name_br'] = "{$item['agent_name_br']} | {$item['agent_family_br']}";
                    $result['image'] = $item['image'];
                }
                elseif ($result['type_name'] == 'weapon')
                {
                    $query = 'SELECT *, weapons_atrributes.id FROM weapons_atrributes
                    LEFT JOIN weapons ON weapons_atrributes.weapon_id = weapons.id
                    WHERE weapons_atrributes.id = :id';
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['id' => $result['type_id']]);
                    $item = $stmt->fetch(\PDO::FETCH_ASSOC);

                    $exterior = [
                        'fn' => ['en' => 'Factory New', 'br' => 'Nova de Fábrica'],
                        'mw' => ['en' => 'Minimal Wear', 'br' => 'Pouca Usada'],
                        'ft' => ['en' => 'Field-Tested', 'br' => 'Testada em Campo'],
                        'ww' => ['en' => 'Well Worm', 'br' => 'Bem Desgastada'],
                        'bs' => ['en' => 'Battle-Scarred', 'br' => 'Veterana de Guerra']
                    ];

                    $stattrak = $item['weapon_stattrak'] ? ' (StatTrak™)' : '';
                    $result['full_name'] = "{$item['weapon_name']}{$stattrak} | {$item['weapon_family']} ({$exterior[$item['weapon_exterior']]['en']})";
                    $result['full_name_br'] = "{$item['weapon_name_br']}{$stattrak} | {$item['weapon_family_br']} ({$exterior[$item['weapon_exterior']]['br']})";
                    $result['image'] = "{$item['image']}_{$item['weapon_exterior']}";
                }

                $cart = $session->get('cart');
                $cart['items'][$result['id']] = $result;
                $session->set('cart', $cart);
            }
        }

        redirect('/cart');
    }

    public function delete()
    {
        $session = Session::create();

        $item_id = $_GET['item_id'] ?? false;

        if ($item_id)
        {
            $cart = $session->get('cart');
            unset($cart['items'][$item_id]);
            $session->set('cart', $cart);
        }

        redirect('/cart');
    }

    public function coupon()
    {
        $session = Session::create();
        $cart = $session->get('cart');

        $coupon = trim($_POST['coupon'] ?? '');

        if (!empty($coupon))
        {
            $pdo = Database::connect();

            $coupon = strtoupper($coupon);

            $query = 'SELECT * FROM coupon WHERE name = :name';
            $stmt = $pdo->prepare($query);
            $stmt->execute(['name' => $coupon]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result)
            {
                $user_id = $session->get('user_id');

                $expiration_date = strtotime($result['expiration_date']);

                if (time() > $expiration_date)
                {
                    $cart['coupon'] = null;
                    $session->set('cart', $cart);
                    $session->flash('coupon', ['message' => 'Cupom expirado.', 'type' => 'failure']);
                    redirect('/cart');
                }

                if (is_null($result['user_id']) || $result['user_id'] == $user_id)
                {
                    $cart['coupon'] = $result;
                    $session->set('cart', $cart);
                    $session->flash('coupon', ['message' => 'Cupom adicionado.', 'type' => 'success']);
                    redirect('/cart');
                }
            }
        }

        $cart['coupon'] = null;
        $session->set('cart', $cart);
        $session->flash('coupon', ['message' => 'Cupom inválido.', 'type' => 'failure']);
        redirect('/cart');
    }
}