<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Cart
{
    public function index()
    {
        $session = Session::create();
        
        $cart_items = $session->get('cart_items');

        if ($cart_items)
        {
            $count = count($cart_items);

            foreach ($cart_items as $index => $item)
            {
                $prices[] = $item['offer_price'] ?? $item['price'];
            }
            
            $subtotal = array_sum($prices);
            $discount = 0;
            $percent = 0;
            $coupon = $session->get('cart_coupon');
            
            if ($coupon)
            {
                $coupon_name = $coupon['name'];
                $discount = $subtotal / 100 * $coupon['percent'];
                $percent = $coupon['percent'];
            }

            $total = $subtotal - $discount;
            $session->set('cart_subtotal', $subtotal);
            $session->set('cart_discount', $discount);
            $session->set('cart_total', $total);
        }

        $message = $session->flash('coupon');
        $message_failure = $session->flash('coupon_failure');

        $content_view = 'cart.phtml';
        $settings_title = 'Carrinho';

        require VIEW . 'layout.phtml';
    }

    public function add()
    {
        $session = Session::create();

        $item_id = $_GET['item_id'] ?? false;

        if ($item_id)
        {
            $pdo = Database::connect();

            $query = 'SELECT * FROM items WHERE id = :id AND availability = 1';
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

                    $result['full_name'] = "{$item['agent_name']} | {$item['agent_category']}";
                    $result['full_name_br'] = "{$item['agent_name_br']} | {$item['agent_category_br']}";
                    $result['image'] = $item['image'];
                }
                elseif ($result['type_name'] == 'weapon')
                {
                    $query = 'SELECT * FROM weapons WHERE id = :id';
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['id' => $result['type_id']]);
                    $item = $stmt->fetch(\PDO::FETCH_ASSOC);

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

        redirect('/cart');
    }

    public function delete()
    {
        $session = Session::create();

        $item_id = $_GET['item_id'] ?? false;

        if ($item_id)
        {
            $cart_items = $session->get('cart_items');
            unset($cart_items[$item_id]);
            $session->set('cart_items', $cart_items);
        }

        redirect('/cart');
    }

    public function coupon()
    {
        $session = Session::create();

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
                    $session->remove('cart_coupon');                    
                    $session->flash('coupon_failure', 'Cupom expirado.');
                    redirect('/cart');
                }

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
        $session->flash('coupon_failure', 'Cupom inválido.');
        redirect('/cart');
    }
}