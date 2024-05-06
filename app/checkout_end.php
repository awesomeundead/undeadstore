<?php

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

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

require ROOT . '/app/checkout_save.php';

$session->remove('cart_items');
$session->remove('cart_subtotal');
$session->remove('cart_discount');
$session->remove('cart_total');
$session->remove('cart_coupon');

redirect("/pay?purchase_id={$purchase_id}");