<?php

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

$session = Session::create();

// Verifica se o usuário está logado
if (!$session->get('logged_in'))
{
    redirect('/auth');
}

$purchase_id = $_GET['purchase_id'];

$pdo = Database::connect();

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

$purchase_discount = (float) $purchase['discount'];
$purchase_total = (float) $purchase['total'];
$purchase_identifier = 'US' . str_pad(strtoupper(base_convert($purchase_id, 10, 36)), 7, 0, STR_PAD_LEFT);

if ($purchase['pay_method'] == 'pix')
{
    require ROOT . '/include/pix.php';

    $params = [
        'key'         => 'f05b1356-f4e4-4d10-8abb-5a1cb3ed5729',
        'description' => '',
        'value'       => $purchase_total,
        'name'        => 'Undead Store',
        'city'        => 'SAO PAULO',
        'identifier'  => $purchase_identifier
    ];

    $code = pix($params);
    $content_view = 'pay_pix.phtml';
}
elseif ($purchase['pay_method'] == 'mercadopago')
{
    require ROOT . '/app/pay_mercadopago.php';
}

$settings_title = 'Pagamento';

require VIEW . 'layout.phtml';