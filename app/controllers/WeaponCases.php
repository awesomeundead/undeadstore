<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class WeaponCases extends Controller
{
    public function index()
    {
        echo $this->templates->render('weapon-cases/index');
    }

    public function buy()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth?redirect=cases');
        }

        $user_id = $session->get('user_id');

        $config = (require ROOT . '/config.php')['mercadopago'];

        $pdo = Database::connect();

        $query = 'SELECT email FROM users WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $params = [
            'id' => $user_id
        ];
        $stmt->execute($params);
        $email = $stmt->fetchColumn();

        $payer = [
            'firstName' => '',
            'lastName' => '',
            'email' => $email
        ];

        $payment_methods = [
            'bankTransfer' => 'all',
            'creditCard' => 'all',
            'maxInstallments' => 12
        ];

        $back_urls = [
            'error' =>  HOST . BASE_PATH . '/cases/error',
            'return' =>  HOST . BASE_PATH . '/cases'
        ];

        $mercadopago['public_key'] = $config['public_key'];
        $mercadopago['amount'] = 5;
        $mercadopago['payer'] = json_encode($payer);
        $mercadopago['payment_methods'] = json_encode($payment_methods);
        $mercadopago['back_urls'] = json_encode($back_urls, JSON_UNESCAPED_SLASHES);

        echo $this->templates->render('weapon-cases/buy', [
            'mercadopago' => $mercadopago
        ]);
    }

    /* 
     *  POST REQUEST
     */
    public function process()
    {
        error_reporting(0);

        header('Content-Type: application/json; charset=utf-8');

        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            exit;
        }

        $user_id = $session->get('user_id');

        $content = trim(file_get_contents('php://input'));
        $request = json_decode($content, true);

        $amount = (float) $request['transaction_amount'];

        // MODIFICAR
        if ($amount % 5 !== 0)
        {
            exit;
        }

        $request['description'] = 'UNDEAD STORE ITEM DIGITAL';
        $request['external_reference'] = 'UNDEADCASE'. str_pad($user_id, 5, 0, STR_PAD_LEFT);
        $idempotency_key = $request['external_reference'] . time();
       
        $config = (require ROOT . '/config.php')['mercadopago'];

        try
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            // REMOVER
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PaymentClient();
            $options = new RequestOptions();
            $options->setCustomHeaders(["X-Idempotency-Key: {$idempotency_key}"]);
            $payment = $client->create($request, $options);

            echo json_encode($payment);
        }
        catch (MPApiException $e)
        {
            $response = $e->getApiResponse();

            file_put_contents(ROOT . '/log/mercadopago.json', json_encode($response->getContent()), FILE_APPEND);

            echo json_encode($response->getContent());
        }
    }
}