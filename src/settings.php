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

$query = 'SELECT * FROM users WHERE id = :id';
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $_SESSION['user']['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$steam_trade_url = $result['steam_trade_url'];
$name = $result['name'];
$email = $result['email'];
$phone = $result['phone'];

$content_view = 'settings.phtml';
$settings_title = 'Configurações';

require VIEW . 'layout.phtml';