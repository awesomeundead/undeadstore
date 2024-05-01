<?php

use Awesomeundead\Undeadstore\Session;

$session = Session::create();

// Verifica se o usuário está logado
if (!$session->get('logged_in'))
{
    redirect('/auth');
}

require ROOT . '/include/pdo.php';

$query = 'SELECT * FROM users WHERE id = :id';
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $session->get('user_id')]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$steam_trade_url = $result['steam_trade_url'];
$name = $result['name'];
$email = $result['email'];
$phone = $result['phone'];

$message = $session->flash('settings');
$content_view = 'settings.phtml';
$settings_title = 'Configurações';

require VIEW . 'layout.phtml';