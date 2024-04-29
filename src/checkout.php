<?php

session_start();

$logged_in = $_SESSION['logged_in'] ?? false;
$cart_items = $_SESSION['cart']['items'] ?? false;

if ($logged_in && $cart_items)
{
    $steamid = $_SESSION['user']['steamid'];
    $steam_name = $_SESSION['user']['personaname'];
    $steam_avatar = $_SESSION['user']['avatar'];
}
else
{
    redirect('/auth?redirect=checkout');
}

$subtotal = $_SESSION['cart']['subtotal'];
$discount = $_SESSION['cart']['discount'];
$total = $_SESSION['cart']['total'];

require ROOT . '/include/pdo.php';

$query = 'SELECT steam_trade_url FROM users WHERE id = :id';
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $_SESSION['user']['id']]);
$steam_trade_url = $stmt->fetchColumn();

if (isset($_SESSION['__flash']))
{
    $message = $_SESSION['__flash'];
    unset($_SESSION['__flash']);
}

$content_view = 'checkout.phtml';
$settings_title = 'Fechar Pedido';

require VIEW . 'layout.phtml';