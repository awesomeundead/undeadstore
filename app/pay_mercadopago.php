<?php

use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

$config = (require ROOT . '/config.php')['mercadopago'];

$fee = round($purchase_total / 100 * $config['fee'], 2);

$query = 'SELECT * FROM purchase_items WHERE purchase_id = :purchase_id';
$stmt = $pdo->prepare($query);
$stmt->execute(['purchase_id' => $purchase_id]);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($list as $item)
{
    $description[] = "1x {$item['item_name']}";
}

$items = [
    [
        'title' => 'Undead Store Item Digital',
        'description' => implode(', ', $description),
        'quantity' => 1,
        'currency_id' => 'BRL',
        'unit_price' =>  $purchase_total + $fee
    ]
];

$payer = [
    'name' => $session->get('steam_name'),
    'surname' => '',
    'email' => ''
];

$payment_methods =
[
    'excluded_payment_methods' => [
        ['id' => 'pix'],
        ['id' => 'bolbradesco'],
        ['id' => 'pec']
    ]
];

$back_urls = [
    'success' => HOST . BASE_PATH . '/payment/success',
    'failure' => HOST . BASE_PATH . "/payment/failure?purchase_id={$purchase_id}"
];

$request = 
[
    'items' => $items,
    'payer' => $payer,
    'auto_return' => 'approved',
    'payment_methods' => $payment_methods,
    'back_urls' => $back_urls,
    'statement_descriptor' => 'Undead Store',
    'external_reference' => $purchase_identifier
];

$access_token = $config['access_token'];
$public_key = $config['public_key'];

MercadoPagoConfig::setAccessToken($access_token);
//MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

$request_options = new RequestOptions();
$request_options->setCustomHeaders(["X-Idempotency-Key: {$purchase_identifier}"]);

$client = new PreferenceClient();
$preference = $client->create($request, $request_options);

$message = $session->flash('payment');
$message_failure = $session->flash('payment_failure');

$content_view = 'pay_mercadopago.phtml';