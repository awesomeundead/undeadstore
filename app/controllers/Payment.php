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

    public function index()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }
        
        $purchase_id = $_GET['id'] ?? null;

        if (!is_numeric($purchase_id))
        {
            redirect();
        }
        
        $pdo = Database::connect();
        
        $query = 'SELECT * FROM purchase WHERE id = :id AND user_id = :user_id';
        $stmt = $pdo->prepare($query);
        $params = [
            'id' => $purchase_id,
            'user_id' => $session->get('user_id')
        ];
        $stmt->execute($params);
        $purchase = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (empty($purchase))
        {
            redirect();
        }

        $config = (require ROOT . '/config.php')['mercadopago']['checkout_bricks'];

        $payer = [
            'firstName' => '',
            'lastName' => '',
            'email' => 'awesome.undead@outlook.com'
        ];

        $mercadopago['public_key'] = $config['public_key'];
        $mercadopago['amount'] = (float) $purchase['total'];
        $mercadopago['payer'] = json_encode($payer);
        
        echo $this->templates->render('payment/index', ['mercadopago' => $mercadopago]);
    }

    /* 
     *  POST REQUEST
     */
    public function process()
    {
        error_reporting(0);

        header('Content-Type: application/json; charset=utf-8');

        $uniqid = uniqid('undeadstore');
        //$uniqid = 'undeadstore669d746bbf687';

        $content = trim(file_get_contents('php://input'));
        $request = json_decode($content, true);
        $request['description'] = 'UNDEAD STORE ITEM DIGITAL';
        $request['external_reference'] = $uniqid;
        $request['callback_url'] = HOST . BASE_PATH . '/payment/callback';
       
        $config = (require ROOT . '/config.php')['mercadopago']['checkout_bricks'];

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
}