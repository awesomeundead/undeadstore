<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class Weaponcase extends Controller
{
    public function index()
    {
        $pdo = Database::connect();

        $query = "SELECT user_id, i.item_name, rarity, image, personaname, avatarhash FROM inventory_historic AS i
                  INNER JOIN weaponcase ON weaponcase.cs_item_variant_id = i.cs_item_variant_id
                  LEFT JOIN users ON users.id = i.user_id
                  WHERE status = 'drop'
                  ORDER BY i.id DESC LIMIT 20";
                  //AND rarity IN ('mythical_weapon', 'legendary_weapon', 'ancient_weapon')
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $winners = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $query = "SELECT user_id, i.item_name, rarity, image, personaname, avatarhash FROM inventory_historic AS i
                  INNER JOIN weaponcase ON weaponcase.cs_item_variant_id = i.cs_item_variant_id
                  LEFT JOIN users ON users.id = i.user_id
                  WHERE status = 'drop' AND rarity IN ('mythical_weapon', 'legendary_weapon', 'ancient_weapon')
                  ORDER BY i.id DESC LIMIT 20";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $best = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo $this->templates->render('weaponcase/index', [
            'winners' => $winners,
            'best' => $best
        ]);
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

        $query = 'SELECT balance FROM wallet WHERE user_id = :user_id';
        $params = ['user_id' => $user_id];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $balance = $stmt->fetchColumn();

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

        echo $this->templates->render('weaponcase/buy', [
            'mercadopago' => $mercadopago,
            'balance' => $balance
        ]);
    }

    /* 
     *  POST REQUEST
     */
    public function process()
    {
        error_reporting(0);

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
        $request['external_reference'] = 'UCASE_UID'. str_pad($user_id, 5, 0, STR_PAD_LEFT);
        $idempotency_key = $request['external_reference'] . time();
       
        $config = (require ROOT . '/config.php')['mercadopago'];

        try
        {
            MercadoPagoConfig::setAccessToken($config['access_token']);
            //MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

            $client = new PaymentClient();
            $options = new RequestOptions();
            $options->setCustomHeaders(["X-Idempotency-Key: {$idempotency_key}"]);
            $payment = $client->create($request, $options);

            json_response($payment);
        }
        catch (MPApiException $e)
        {
            $response = $e->getApiResponse();

            file_put_contents(ROOT . '/log/mercadopago.json', json_encode($response->getContent()), FILE_APPEND);

            json_response($response->getContent());
        }
    }

    public function buy_with_coins()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth?redirect=cases');
        }

        $user_id = $session->get('user_id');

        $pdo = Database::connect();

        $query = 'SELECT balance FROM wallet WHERE user_id = :user_id';
        $params = ['user_id' => $user_id];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $balance = $stmt->fetchColumn();

        $quantity = floor($balance / 5);

        if ($quantity > 8)
        {
            $quantity = 8;
        }

        echo $this->templates->render('weaponcase/buy-with-coins', [
            'balance' => $balance,
            'quantity' => $quantity
        ]);
    }

    /* 
     *  POST REQUEST
     */
    public function buy_with_coins_process()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            json_response(['redirect' => '/cases']);
        }

        $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);

        if ($quantity == false)
        {
            $quantity = 1;
        }

        if ($quantity > 10)
        {
            $quantity = 10;
        }

        $user_id = $session->get('user_id');
        $date = date('Y-m-d H:i:s');

        $pdo = Database::connect();

        $query = 'SELECT balance FROM wallet WHERE user_id = :user_id';
        $params = ['user_id' => $user_id];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $balance = $stmt->fetchColumn();

        if ($balance >= $quantity * 5)
        {
            $query = 'UPDATE wallet SET balance = :balance WHERE user_id = :user_id';
            $params = [
                'user_id' => $user_id,
                'balance' => $balance - ($quantity * 5)
            ];
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            if ($stmt->rowCount())
            {
                for ($i = 0; $i < $quantity; $i++)
                {
                    $query = 'INSERT INTO inventory (user_id, item_name, tradable, marketable, created_date)
                              VALUES (:user_id, :item_name, :tradable, :marketable, :created_date)';
                    $params = [
                        'user_id' => $user_id,
                        'item_name' => 'undeadcase',
                        'tradable' => 0,
                        'marketable' => 0,
                        'created_date' => $date
                    ];
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);

                    $historic_id = $pdo->lastInsertId();

                    // histórico
                    $query = 'INSERT INTO inventory_historic (historic_id, user_id, item_name, status, date)
                              VALUES (:historic_id, :user_id, :item_name, :status, :date)';
                    $params = [
                        'historic_id' => $historic_id,
                        'user_id' => $user_id,
                        'item_name' => 'undeadcase',
                        'status' => 'purchased',
                        'date' => $date
                    ];

                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                }

                json_response(['redirect' => '/inventory']);
            }            
        }

        json_response(['error' => true]);
    }
}