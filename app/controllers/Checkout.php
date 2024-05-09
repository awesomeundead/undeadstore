<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Checkout
{
    public function index()
    {
        $session = Session::create();

        $logged_in = $session->get('logged_in');
        $cart_items = $session->get('cart_items');

        if (!$logged_in || !$cart_items)
        {
            redirect('/auth?redirect=checkout');
        }

        $subtotal = $session->get('cart_subtotal');
        $discount = $session->get('cart_discount');
        $total = $session->get('cart_total');

        $pdo = Database::connect();

        $query = 'SELECT steam_trade_url FROM users WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $session->get('user_id')]);
        $steam_trade_url = $stmt->fetchColumn();
        $steamid = $session->get('steamid');

        $message = $session->flash('trade');
        $message_failure = $session->flash('trade_failure');

        $content_view = 'checkout.phtml';
        $settings_title = 'Fechar Pedido';

        require VIEW . 'layout.phtml';
    }

    public function end()
    {
        $session = Session::create();

        $logged_in = $session->get('logged_in');
        $cart_items = $session->get('cart_items');

        if (!$logged_in || !$cart_items)
        {
            redirect('/auth?redirect=pay');
        }

        $coupon = $session->get('cart_coupon');

        if ($coupon)
        {
            $expiration_date = strtotime($coupon['expiration_date']);
            $timestamp = time();

            if ($timestamp > $expiration_date)
            {
                $session->remove('cart_coupon');
                $session->flash('coupon_failure', 'Cupom expirado.');

                redirect('/cart');
            }
        }

        $pdo = Database::connect();

        $query = 'SELECT steam_trade_url FROM users WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $session->get('user_id')]);
        $steam_trade_url = $stmt->fetchColumn();

        if (!$steam_trade_url)
        {
            // Mensagem de url de troca vazia
            $session->flash('trade_failure', 'Não deixe o campo URL vazio.');
            redirect('/checkout');
        }

        $coupon = $coupon['name'] ?? '';
        $subtotal = $session->get('cart_subtotal');
        $discount = $session->get('cart_discount');
        $total = $session->get('cart_total');

        $pay_method = $_GET['pay_method'] ?? false;

        if ($pay_method != 'pix' && $pay_method != 'mercadopago')
        {
            redirect('/');
        }

        $query = 'INSERT INTO purchase (user_id, pay_method, status, coupon, subtotal, discount, total, created_date)
            VALUES (:user_id, :pay_method, :status, :coupon, :subtotal, :discount, :total, :created_date)';
        $stmt = $pdo->prepare($query);
        $params = [
            'user_id' => $session->get('user_id'),
            'pay_method' => $pay_method,
            'status' => 'Em andamento',
            'coupon' => $coupon,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'created_date' => date('Y-m-d H:i:s')
        ];
        $result = $stmt->execute($params);

        if (!$result)
        {
            $session->flash('trade_failure', 'Ocorreu um erro.');
            redirect('/checkout');
        }

        $purchase_id = $pdo->lastInsertId();

        // $item [id, item_id, item_name, availability, price, offer_price, image]
        foreach ($cart_items as $item)
        {
            $query = 'INSERT INTO purchase_items (purchase_id, item_id, item_name, price, offer_price) VALUES (:purchase_id, :item_id, :item_name, :price, :offer_price)';
            $stmt = $pdo->prepare($query);
            $params = [
                'purchase_id' => $purchase_id,
                'item_id' => $item['id'],
                'item_name' => $item['full_name'],
                'price' => $item['price'],
                'offer_price' => $item['offer_price']
            ];
            $stmt->execute($params);

            $query = 'UPDATE items SET availability = :availability, price = :price WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $params = [
                'id' => $item['id'],
                'availability' => 2,
                'price' => null
            ];
            $stmt->execute($params);
        }

        $session->remove('cart_items');
        $session->remove('cart_subtotal');
        $session->remove('cart_discount');
        $session->remove('cart_total');
        $session->remove('cart_coupon');

        redirect("/pay?purchase_id={$purchase_id}");
    }

    public function trade()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $steam_trade_url = trim($_POST['steam_trade_url'] ?? '');

        if (preg_match('#^https://steamcommunity.com/tradeoffer/new/\?partner=(\d+)&token=(\w+)$#', $steam_trade_url, $matches))
        {
            $pdo = Database::connect();

            $query = 'UPDATE users SET steam_trade_url = :steam_trade_url WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $params = [
                'id' => $session->get('user_id'),
                'steam_trade_url' => $steam_trade_url
            ];
            $stmt->execute($params);
            $session->flash('trade', 'Atualizado');
        }
        else
        {
            $session->flash('trade_failure', 'URL inválida');
        }
        
        redirect('/checkout');
    }
}