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
    redirect('/auth?redirect=pay');
}


$coupon = $_SESSION['cart']['coupon'] ?? false;

if ($coupon)
{
    $expiration_date = strtotime($coupon['expiration_date']);
    $timestamp = time();

    if ($timestamp > $expiration_date)
    {
        $_SESSION['cart']['coupon'] = false;
        redirect('/cart?err=Cupom expirado.');
    }
}

require ROOT . '/include/pdo.php';

$query = 'SELECT steam_trade_url FROM users WHERE id = :id';
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $_SESSION['user']['id']]);
$steam_trade_url = $stmt->fetchColumn();

if (!$steam_trade_url)
{
    // Mensagem de url de troca vazia
    $_SESSION['__flash'] = 'Não deixe o campo URL vazio.';
    redirect('/checkout');
}

$coupon = $coupon['name'] ?? '';
$subtotal = $_SESSION['cart']['subtotal'];
$discount = $_SESSION['cart']['discount'];
$total = $_SESSION['cart']['total'];

$query = 'INSERT INTO purchase (user_id, pay_method, pay_progress, coupon, subtotal, discount, total, created_date)
            VALUES (:user_id, :pay_method, :pay_progress, :coupon, :subtotal, :discount, :total, :created_date)';
$stmt = $pdo->prepare($query);
$params = [
    'user_id' => $_SESSION['user']['id'],
    'pay_method' => 'PIX',
    'pay_progress' => 'Em andamento',
    'coupon' => $coupon,
    'subtotal' => $subtotal,
    'discount' => $discount,
    'total' => $total,
    'created_date' => date('Y-m-d H:i:s')
];
$result = $stmt->execute($params);

if (!$result)
{
    redirect('/checkout?error');
}

$purchase_id = $pdo->lastInsertId();
print_r($cart_items);
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

unset($_SESSION['cart']);

redirect("/pay?purchase_id={$purchase_id}");