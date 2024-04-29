<?php

require ROOT . '/include/pdo.php';

// VERIFICA SE ESSE USUÁRIO JÁ É REGISTRADO
$query = 'SELECT id FROM users WHERE steamid = :steamid';
$stmt = $pdo->prepare($query);
$stmt->execute(['steamid' => $_SESSION['user']['steamid']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result)
{
    // USUÁRIO JÁ REGISTRADO
    $_SESSION['user']['id'] = $result['id'];
}
else
{
    // NOVO REGISTRO DE USUÁRIO
    $query = 'INSERT INTO users (steamid, created_date) VALUES (:steamid, :created_date)';
    $stmt = $pdo->prepare($query);
    $params = [
        'steamid' => $_SESSION['user']['steamid'],
        'created_date' => date('Y-m-d')
    ];
    $result = $stmt->execute($params);

    if ($result)
    {
        $_SESSION['user']['id'] = $pdo->lastInsertId();
    }
}

$address = $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_X_FORWARDED']
        ?? $_SERVER['HTTP_FORWARDED_FOR']
        ?? $_SERVER['HTTP_FORWARDED']
        ?? $_SERVER['REMOTE_ADDR']
        ?? 'UNKNOWN';

// NOVO REGISTRO DE LOGIN
$query = 'INSERT INTO login_log (user_id, login_date, user_ip) VALUES (:user_id, :login_date, :user_ip)';
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['user']['id'], 'login_date' => date('Y-m-d H:i:s'), 'user_ip' => $address]);