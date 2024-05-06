<?php

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

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