<?php

use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

$fees = $purchase_total / 100 * 4.98;

$items = [
    [
        'title' => 'Undead Store Item Digital',
        'description' => '',
        'picture_url' => '',
        'category_id' => '',
        'quantity' => 1,
        'currency_id' => 'BRL',
        'unit_price' =>  $purchase_total + $fees
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
    'payment_methods' => $payment_methods,
    'back_urls' => $back_urls,
    'statement_descriptor' => 'Undead Store',
    'external_reference' => $purchase_identifier
    //'coupon_amount' => ''
];

try
{
    MercadoPagoConfig::setAccessToken('TEST-7407069493848525-043015-b6eef5c68c01daf9e227f8ef3727ae45-234415597');
    MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

    $request_options = new RequestOptions();
    $request_options->setCustomHeaders(["X-Idempotency-Key: {$purchase_identifier}"]);

    $client = new PreferenceClient();
    $preference = $client->create($request, $request_options);
    
    $message = $session->flash('payment');
    $content_view = 'pay_mercadopago.phtml';
}
catch (MPApiException $error)
{
    $error->getMessage();
}