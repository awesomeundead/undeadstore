<?php

$data_id    = '1325156321';
$xSignature = 'ts=1721313691,v1=3f1bdd1cb9f03fb21cbe2ef677d13f7e343cdb47eafb2306a5bc0aaace06fab9';
$xRequestId = 'f643f203-269e-40a4-8ab4-ae93f7ab0166';

if (preg_match('/^ts=(?P<ts>\d+),v1=(?P<hash>\w{64})$/', $xSignature, $matches))
{
    $config = (require '../config.php')['mercadopago']['checkout_bricks'];
    $access_token = $config['access_token'];
    $secret = $config['secret_signature'];

    $manifest = "id:{$data_id};request-id:{$xRequestId};ts:{$matches['ts']};";
    $sha = hash_hmac('sha256', $manifest, $secret);

    if ($sha === $matches['hash'])
    {
        $context = stream_context_create([
            'http' => [
                'header' => 'Authorization: Bearer ' . $access_token
            ],
        ]);

        header('Content-Type: application/json; charset=utf-8');
        
        echo file_get_contents('https://api.mercadopago.com/v1/payments/' . $data_id, false, $context);
    }
}