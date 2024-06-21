<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Checkout extends Controller
{
    public function index()
    {
        $session = Session::create();

        $logged_in = $session->get('logged_in');
        $cart = $session->get('cart');

        if (!$logged_in || !$cart['items'])
        {
            redirect('/auth?redirect=checkout');
        }

        $pdo = Database::connect();

        $query = 'SELECT steam_trade_url FROM users WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $session->get('user_id')]);
        $steam_trade_url = $stmt->fetchColumn();

        echo $this->templates->render('checkout/index', [
            'notification' => $session->flash('trade'),
            'steam_trade_url' => $steam_trade_url,
            'steamid' => $session->get('steamid'),
            'subtotal' => $cart['subtotal'],
            'discount' => $cart['discount'],
            'total' => $cart['total']
        ]);
    }

    public function end()
    {
        $session = Session::create();

        $logged_in = $session->get('logged_in');
        $cart = $session->get('cart');

        if (!$logged_in || !$cart['items'])
        {
            redirect('/auth?redirect=pay');
        }

        $coupon = $cart['coupon'] ?? false;

        if ($coupon)
        {
            $expiration_date = strtotime($coupon['expiration_date']);
            $timestamp = time();

            if ($timestamp > $expiration_date)
            {
                $cart['coupon'] = null;
                $session->set('cart', $cart);
                $session->flash('coupon', ['message' => 'Cupom expirado.', 'type' => 'failure']);

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
            $session->flash('trade', ['message' => 'Não deixe o campo URL vazio.', 'type' => 'failure']);
            redirect('/checkout');
        }

        $coupon = $coupon['name'] ?? '';
        $subtotal = $cart['subtotal'];
        $discount = $cart['discount'];
        $total = $cart['total'];

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
            'status' => 'pending',
            'coupon' => $coupon,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'created_date' => date('Y-m-d H:i:s')
        ];
        $result = $stmt->execute($params);

        if (!$result)
        {
            $session->flash('trade', ['message' => 'Ocorreu um erro.', 'type' => 'failure']);
            redirect('/checkout');
        }

        $purchase_id = $pdo->lastInsertId();

        // $item [id, item_id, item_name, availability, price, offer_price, image]
        foreach ($cart['items'] as $item)
        {
            $query = 'INSERT INTO purchase_items (purchase_id, product_id, status, item_name, price, offer_price) VALUES (:purchase_id, :product_id, :status, :item_name, :price, :offer_price)';
            $stmt = $pdo->prepare($query);
            $params = [
                'purchase_id' => $purchase_id,
                'product_id' => $item['id'],
                'status' => 'pending',
                'item_name' => $item['market_hash_name'],
                'price' => $item['price'],
                'offer_price' => $item['offer_price']
            ];
            $stmt->execute($params);

            $query = 'UPDATE products SET availability = :availability WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $params = [
                'id' => $item['id'],
                'availability' => 2
            ];
            $stmt->execute($params);
        }

        $session->remove('cart');

        require ROOT . '/include/mail.php';

        $steamid = $session->get('steamid');
        $personaname = $session->get('steam_name');
        $params['subject'] = 'Nova venda registrada';
        $params['message'] = "O usuário {$personaname} ({$steamid}), fez uma compra.";
        
        send_mail($params);

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
            $session->flash('trade', ['message' => 'Atualizado', 'type' => 'success']);
        }
        else
        {
            $session->flash('trade', ['message' => 'URL inválida.', 'type' => 'failure']);
        }
        
        redirect('/checkout');
    }
}