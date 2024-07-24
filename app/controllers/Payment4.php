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
    private function check_purchase($purchase_id, $user_id)
    {
        $pdo = Database::connect();
        
        $query = 'SELECT * FROM purchase WHERE id = :id AND user_id = :user_id';
        $stmt = $pdo->prepare($query);
        $params = [
            'id' => $purchase_id,
            'user_id' => $user_id
        ];
        $stmt->execute($params);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function index()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }
        
        $purchase_id = $_GET['id'] ?? null;
        $user_id = $session->get('user_id');

        if (!is_numeric($purchase_id))
        {
            redirect();
        }
        
        $purchase = $this->check_purchase($purchase_id, $user_id);
        
        if (empty($purchase))
        {
            redirect();
        }

        // remover
        $config = (require ROOT . '/config.php')['mercadopago']['checkout_bricks'];

        if ($purchase['payment_id'])
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            // remover
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PaymentClient();
            $payment = $client->get($purchase['payment_id']);

            if ($payment->status == 'approved')
            {
                //redirect('/order-history');
            }
        }

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
            'error' =>  HOST . BASE_PATH . '/payment/update?id=' . $purchase_id,
            'return' =>  HOST . BASE_PATH . '/order-history'
        ];

        /*
        $credit_card = ['master', 'visa', 'hipercard', 'elo', 'amex'];

        if ($purchase['payment_method'] == 'pix')
        {
            $payment_methods = [
                'bankTransfer' => $purchase['payment_method']
            ];
        }
        elseif(in_array($purchase['payment_method'], $credit_card))
        {
            $payment_methods = [
                'creditCard' => $purchase['payment_method'],
                'maxInstallments' => 12
            ];
        }
        else
        {
            
        }
        */

        $mercadopago['public_key'] = $config['public_key'];
        $mercadopago['amount'] = (float) $purchase['total'];
        $mercadopago['payer'] = json_encode($payer);
        $mercadopago['payment_methods'] = json_encode($payment_methods);
        $mercadopago['payment_id'] = $purchase['payment_id'] ?? 'null';
        $mercadopago['back_urls'] = json_encode($back_urls, JSON_UNESCAPED_SLASHES);
        
        echo $this->templates->render('payment/index', [
            'mercadopago' => $mercadopago,
            'purchase_id' => $purchase_id,
            'payment_status' => $payment->status ?? null
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

        $purchase_id = $_GET['id'] ?? null;
        $user_id = $session->get('user_id');

        if (!is_numeric($purchase_id))
        {
            exit;
        }

        $purchase = $this->check_purchase($purchase_id, $user_id);
        
        if (empty($purchase))
        {
            exit;
        }

        $content = trim(file_get_contents('php://input'));
        $request = json_decode($content, true);

        file_put_contents(ROOT . '/log/request.json', $content);

        if ($request['transaction_amount'] != $purchase['total'])
        {
            exit;
        }

        $request['description'] = 'UNDEAD STORE ITEM DIGITAL';
        //$request['external_reference'] = $purchase['external_reference'];
        $request['external_reference'] = create_external_reference($purchase_id);
        $idempotency_key = $request['external_reference'] . time();
        //$request['callback_url'] = HOST . BASE_PATH . '/order-history';
       
        // remover
        $config = (require ROOT . '/config.php')['mercadopago']['checkout_bricks'];

        try
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            // remover
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
        }
    }

    /* 
     *  POST REQUEST
     */
    public function notification()
    {

    }

    public function update()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }
        
        $purchase_id = $_GET['id'] ?? null;
        $user_id = $session->get('user_id');

        if (!is_numeric($purchase_id))
        {
            redirect();
        }
        
        $purchase = $this->check_purchase($purchase_id, $user_id);
        
        if (empty($purchase))
        {
            redirect();
        }
        
        // remover
        $config = (require ROOT . '/config.php')['mercadopago']['checkout_bricks'];

        MercadoPagoConfig::setAccessToken($config['access_token']);
        // remover
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        $client = new PaymentClient();
        $payment = $client->get($purchase['payment_id']);

        if ($payment->status == 'approved' || $payment->status == 'in_process')
        {
            redirect('/order-history');
        }

        $pdo = Database::connect();

        $query = 'UPDATE purchase
                  SET status = :status, payment_method = :payment_method, payment_id = :payment_id, external_reference = :external_reference
                  WHERE id = :id AND user_id = :user_id';
        $stmt = $pdo->prepare($query);
        $params = [
            'id' => $purchase_id,
            'user_id' => $user_id,
            'payment_method' => null,
            'payment_id' => null,
            'external_reference' => create_external_reference($purchase_id, $purchase['external_reference']),
            'status' => 'pending'
        ];
        $result = $stmt->execute($params);
        $result = true;

        if ($result)
        {
            /*
            // remover
            $config = (require ROOT . '/config.php')['mercadopago']['checkout_bricks'];

            $data = json_encode(['status' => 'cancelled']);
            $context = stream_context_create([
                'http' => [
                    'method' => 'PUT',
                    'header' => "Content-type: application/json; charset=UTF-8\r\n" .
                                'Authorization: Bearer ' . $config['access_token'],
                    'content' => $data,
                    'ignore_errors' => true
                ]
            ]);
            
            $response = file_get_contents('https://api.mercadopago.com/v1/payments/' . $purchase['payment_id'], false, $context);
            print_r($http_response_header);
            file_put_contents(ROOT . '/log/update.json', $response, FILE_APPEND);
            */
        }

        redirect('/payment?id=' . $purchase_id);
    }
}