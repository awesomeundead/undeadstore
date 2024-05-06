<?php

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

$session = Session::create();

// Verifica se o usuário está logado
if (!$session->get('logged_in'))
{
    redirect('/auth');
}

$pdo = Database::connect();

$query = 'SELECT * FROM purchase WHERE user_id = :user_id ORDER BY id DESC';
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $session->get('user_id')]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $index => $item)
{
    $query = 'SELECT * FROM purchase_items WHERE purchase_id = :purchase_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['purchase_id' => $item['id']]);
    $result[$index]['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$message = $session->flash('payment');
$message_failure = $session->flash('payment_failure');

$content_view = 'orders.phtml';
$settings_title = 'Pedidos';

require VIEW . 'layout.phtml';