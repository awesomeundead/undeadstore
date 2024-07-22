<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class Payment extends Controller
{
    public function index()
    {
        $config = (require ROOT . '/config.php')['mercadopago'];

        $mercadopago['public_key'] = $config['public_key'];

        echo $this->templates->render('payment/index', ['mercadopago' => $mercadopago]);
    }

    public function process()
    {
        error_reporting(0);

        header('Content-Type: application/json; charset=utf-8');

        $content = trim(file_get_contents('php://input'));
        $request = json_decode($content, true);

        file_put_contents(ROOT . '/request.json', $content);

        $config = (require ROOT . '/config.php')['mercadopago'];

        try
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            //MercadoPagoConfig::setAccessToken('APP_USR-7407069493848525-043015-dfebb4b1a40be6a83a2aeb563cfb3aa6-234415597');
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PaymentClient();
            $payment = $client->create($request);

            file_put_contents(ROOT . '/payment.json', json_encode($payment));
            echo json_encode($payment);
        }
        catch (MPApiException $e)
        {
            $content = "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
            $content .=  "Content: " . var_export($e->getApiResponse()->getContent(), true) . "\n";

            file_put_contents(ROOT . 'log.txt', $content);
        }
    }

    // Checkout Bricks Payment
    public function index_mp_payment()
    {
        $config = (require ROOT . '/config.php')['mercadopago'];
        $uniqid = uniqid('pay', true);

        $amount = 100;

        $items = [
            [
                'title' => 'Undead Store - Item Digital',
                'description' => '',
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' =>  $amount
            ]
        ];

        $payer = [
            'email' => 'awesome.undead@outlook.com'
        ];

        $payment_methods =
        [
            'excluded_payment_methods' => [
                //['id' => 'pix'],
                ['id' => 'bolbradesco'],
                ['id' => 'debit_card'],
                ['id' => 'pec']
            ]
        ];

        $back_urls = [
            //'success' => HOST . BASE_PATH . '/payment/success',
            //'failure' => HOST . BASE_PATH . "/payment/failure?purchase_id={$purchase['id']}",
            //'pending' => HOST . BASE_PATH . '/payment/pending'
        ];

        $request = 
        [
            'external_reference' => $uniqid,
            'items' => $items,
            //'notification_url' => '/payment/notification',
            //'payer' => $payer,
            //'payment_methods' => $payment_methods,
            //'back_urls' => $back_urls,
            'statement_descriptor' => 'Undead Store',
            'installments' => 12
        ];

        try
        {
            //$config['public_key'] = 'APP_USR-e98badbc-0d4b-4067-80b6-5210dfd6cc7d';
            MercadoPagoConfig::setAccessToken($config['access_token']);
            //MercadoPagoConfig::setAccessToken('APP_USR-7407069493848525-043015-dfebb4b1a40be6a83a2aeb563cfb3aa6-234415597');
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PreferenceClient();
            $options = new RequestOptions();
            $options->setCustomHeaders(["X-Idempotency-Key: {$uniqid}"]);
            $preference = $client->create($request, $options);
        }
        catch (MPApiException $e)
        {
            echo "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
            echo "Content: ";
            var_dump($e->getApiResponse()->getContent());
            echo "\n";
        }
        
        echo $this->templates->render('payment/index_mp_payment', [
            'public_key' => $config['public_key'],
            'preference' => $preference,
            'amount' => $amount,
            'payer' => json_encode($payer)
        ]);
    }

    // Checkout Bricks Payment
    public function process_mp_payment()
    {
        error_reporting(0);

        header('Content-Type: application/json; charset=utf-8');

        $content = trim(file_get_contents('php://input'));
        $data = json_decode($content, true);
       
        $config = (require ROOT . '/config.php')['mercadopago'];

        file_put_contents(ROOT . 'request.json', $content);

        $request = $data;

        try
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            //MercadoPagoConfig::setAccessToken('APP_USR-7407069493848525-043015-dfebb4b1a40be6a83a2aeb563cfb3aa6-234415597');
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PaymentClient();
            $payment = $client->create($request);

            file_put_contents('payment.json', json_encode($payment));
            echo json_encode($payment);
        }
        catch (MPApiException $e)
        {
            $content = "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
            $content .=  "Content: " . var_export($e->getApiResponse()->getContent(), true) . "\n";

            file_put_contents(ROOT . 'log.txt', $content);
        }
    }
}