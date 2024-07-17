<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Cart extends Controller
{
    public function index()
    {
        header('X-Robots-Tag: noindex, nofollow');
        
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

            $query = 'SELECT *, products.id,
            IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price
            FROM products
            LEFT JOIN cs_item_variant ON products.cs_item_variant_id = cs_item_variant.id
            LEFT JOIN cs_item ON cs_item_variant.cs_item_id = cs_item.id
            WHERE products.id = :id AND availability = 1';
            
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $item_id]);
            $item = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($item)
            {
                if ($item['price'] != null)
                {
                    if ($item['type'] == 'Agent')
                    {
                        $item['full_name_br'] = "{$item['name_br']} | {$item['family_br']}";
                    }
                    else
                    {
                        $categories = [
                            'normal' => ['en' => 'Normal', 'br' => 'Normal'],
                            'tournament' => ['en' => 'Souvenir', 'br' => 'Lembrança'],
                            'strange' => ['en' => 'StatTrak™', 'br' => 'StatTrak™'],
                            'unusual' => ['en' => '★', 'br' => '★'],
                            'unusual_strange' => ['en' => '★ StatTrak™', 'br' => '★ StatTrak™']
                        ];

                        $category = $item['category'] == 'normal' ? '' : " {$categories[$item['category']]['br']}";

                        $exterior = [
                            'fn' => ['en' => 'Factory New', 'br' => 'Nova de Fábrica'],
                            'mw' => ['en' => 'Minimal Wear', 'br' => 'Pouca Usada'],
                            'ft' => ['en' => 'Field-Tested', 'br' => 'Testada em Campo'],
                            'ww' => ['en' => 'Well Worm', 'br' => 'Bem Desgastada'],
                            'bs' => ['en' => 'Battle-Scarred', 'br' => 'Veterana de Guerra']
                        ];

                        $item['full_name_br'] = "{$item['name_br']}{$category} | {$item['family_br']} ({$exterior[$item['exterior']]['br']})";
                        $item['image'] = "{$item['image']}_{$item['exterior']}";
                    }

                    $cart = $session->get('cart');
                    $cart['items'][$item['id']] = $item;
                    $session->set('cart', $cart);
                }
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