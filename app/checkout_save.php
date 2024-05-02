<?php

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
    $session->flash('trade', 'Ocorreu um erro.');
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