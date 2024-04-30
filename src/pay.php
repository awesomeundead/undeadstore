<?php

require ROOT . '/include/check_login.php';

$purchase_id = $_GET['purchase_id'];

require ROOT . '/include/pdo.php';

$query = 'SELECT * FROM purchase WHERE id = :id AND user_id = :user_id';
$stmt = $pdo->prepare($query);
$params = [
    'id' => $_GET['purchase_id'],
    'user_id' => $session->get('user_id')
];
$stmt->execute($params);
$purchase = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($purchase))
{
    redirect();
}

require ROOT . '/include/pix.php';

$params = [
    'key'         => 'f05b1356-f4e4-4d10-8abb-5a1cb3ed5729',
    'description' => '',
    'value'       => $purchase['total'],
    'name'        => 'Undead Store',
    'city'        => 'SAO PAULO',
    'identifier'  => 'US' . str_pad(strtoupper(base_convert($purchase_id, 10, 36)), 7, 0, STR_PAD_LEFT)
];

$code = pix($params);
$total = $purchase['total'];

$content_view = 'pay.phtml';
$settings_title = 'Pagamento';

require VIEW . 'layout.phtml';