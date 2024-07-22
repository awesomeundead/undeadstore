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
    private function mp_exception($exception)
    {
        $response = $exception->getApiResponse();

        file_put_contents(ROOT . '/log.json', json_encode($response->getContent()));

        //http_response_code($response->getStatusCode());
        return json_encode($response->getContent());
    }
    // Checkout Bricks CardPayment
    public function index()
    {
        $config = (require ROOT . '/config.php')['mercadopago']['checkout_bricks'];

        $mercadopago['public_key'] = $config['public_key'];

        echo $this->templates->render('payment/index', ['mercadopago' => $mercadopago]);
    }

    // Checkout Bricks CardPayment
    public function process()
    {
        error_reporting(0);

        header('Content-Type: application/json; charset=utf-8');

        $content = trim(file_get_contents('php://input'));
        $request = json_decode($content, true);

        $uniqid = uniqid('undeadstore');
        //$request['external_reference'] = $uniqid;

        file_put_contents(ROOT . '/request.json', $content);
        //file_put_contents(ROOT . '/payment.txt', print_r($request, true));
        //exit;

        $config = (require ROOT . '/config.php')['mercadopago']['checkout_bricks'];

        try
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PaymentClient();
            $payment = $client->create($request);

            file_put_contents(ROOT . '/payment.json', json_encode($payment));
            echo json_encode($payment);
        }
        catch (MPApiException $e)
        {
            echo $this->mp_exception($e);
        }
    }

    // Checkout Bricks Payment
    public function index_mp_payment()
    {
        $config = (require ROOT . '/config.php')['mercadopago']['production'];

        $payer = [
            'firstName' => '',
            'lastName' => '',
            'email' => 'undead.gamer@outlook.com'
        ];

        $mercadopago['public_key'] = $config['public_key'];
        $mercadopago['amount'] = 2;
        $mercadopago['payer'] = json_encode($payer);
        
        echo $this->templates->render('payment/index_mp_payment', ['mercadopago' => $mercadopago]);
    }

    // Checkout Bricks Payment
    public function process_mp_payment()
    {
        error_reporting(0);

        header('Content-Type: application/json; charset=utf-8');

        $uniqid = uniqid('undeadstore');        
        $uniqid = 'undeadstore669d746bbf687';

        $content = trim(file_get_contents('php://input'));
        $request = json_decode($content, true);
        $request['external_reference'] = $uniqid;
       
        $config = (require ROOT . '/config.php')['mercadopago']['production'];

        file_put_contents(ROOT . '/request.json', $content);

        try
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PaymentClient();
            $options = new RequestOptions();
            $options->setCustomHeaders(["X-Idempotency-Key: {$uniqid}"]);
            $payment = $client->create($request, $options);

            file_put_contents(ROOT . '/payment.json', json_encode($payment));
            echo json_encode($payment);
        }
        catch (MPApiException $e)
        {
            echo $this->mp_exception($e);
        }
    }

    public function mp_pix()
    {
        $config = (require ROOT . '/config.php')['mercadopago']['production'];
        $uniqid = uniqid('undeadstore');

        $request = [
            'transaction_amount' => 5.5,
            'description' => 'description',
            'external_reference' => $uniqid,
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => 'awesome.undead@outlook.com'
            ]
        ];

        try
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PaymentClient();
            $payment = $client->create($request);

            //echo json_encode($payment);
        }
        catch (MPApiException $e)
        {
            echo $this->mp_exception($e);
        }
        

        echo $this->templates->render('payment/index_mp_pix', ['payment' => $payment ?? []]);
    }

}