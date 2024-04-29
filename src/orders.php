<?php

session_start();

$logged_in = $_SESSION['logged_in'] ?? false;

if ($logged_in)
{
    $steamid = $_SESSION['user']['steamid'];
    $steam_name = $_SESSION['user']['personaname'];
    $steam_avatar = $_SESSION['user']['avatar'];
}
else
{
    redirect('/auth');
}

require ROOT . '/include/pdo.php';

$query = 'SELECT * FROM purchase WHERE user_id = :user_id ORDER BY id DESC';
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['user']['id']]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $index => $item)
{
    $query = 'SELECT * FROM purchase_items WHERE purchase_id = :purchase_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['purchase_id' => $item['id']]);
    $result[$index]['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$content_view = 'orders.phtml';
$settings_title = 'Pedidos';

require VIEW . 'layout.phtml';