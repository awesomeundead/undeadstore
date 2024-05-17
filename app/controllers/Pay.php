<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class Pay extends Controller
{
    private function _mercadopago($purchase)
    {
        $session = Session::create();

        $config = (require ROOT . '/config.php')['mercadopago'];

        $purchase['fee'] = round($purchase['total'] / 100 * $config['fee'], 2);

        $pdo = Database::connect();

        $query = 'SELECT * FROM purchase_items WHERE purchase_id = :purchase_id';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['purchase_id' => $purchase['id']]);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
                'unit_price' =>  $purchase['total'] + $purchase['fee']
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
            'failure' => HOST . BASE_PATH . "/payment/failure?purchase_id={$purchase['id']}",
            'pending' => HOST . BASE_PATH . '/payment/pending'
        ];

        $request = 
        [
            'items' => $items,
            'payer' => $payer,
            'auto_return' => 'approved',
            'payment_methods' => $payment_methods,
            'back_urls' => $back_urls,
            'statement_descriptor' => 'Undead Store',
            'external_reference' => $purchase['identifier']
        ];

        MercadoPagoConfig::setAccessToken($config['access_token']);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        $request_options = new RequestOptions();
        $request_options->setCustomHeaders(["X-Idempotency-Key: {$purchase['identifier']}"]);

        $client = new PreferenceClient();
        $preference = $client->create($request, $request_options);

        echo $this->templates->render('payment/mercadopago', [
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name')
            ],
            'description' => $description,
            'preference' => $preference,
            'public_key' => $config['public_key'],
            'purchase' => $purchase,
            'notification' => $session->flash('payment')
        ]);
    }

    private function _pix($purchase)
    {
        $session = Session::create();

        require ROOT . '/include/pix.php';
        
        $params = [
            'key'         => 'f05b1356-f4e4-4d10-8abb-5a1cb3ed5729',
            'description' => '',
            'value'       => $purchase['total'],
            'name'        => 'Undead Store',
            'city'        => 'SAO PAULO',
            'identifier'  => $purchase['identifier']
        ];
    
        $code = pix($params);

        echo $this->templates->render('payment/pix', [
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name')
            ],
            'code' => $code,
            'purchase' => $purchase,
            'purchase_total' => $purchase['total']
        ]);
    }

    public function index()
    {
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
        $purchase = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (empty($purchase))
        {
            redirect();
        }
        
        $purchase['discount']= (float) $purchase['discount'];
        $purchase['total'] = (float) $purchase['total'];
        $purchase['identifier'] = 'US' . str_pad(strtoupper(base_convert($purchase_id, 10, 36)), 7, 0, STR_PAD_LEFT);
        
        if ($purchase['pay_method'] == 'pix')
        {
            $this->_pix($purchase);
        }
        elseif ($purchase['pay_method'] == 'mercadopago')
        {
            $this->_mercadopago($purchase);
        }
    }

    public function failure()
    {
        $purchase_id = $_GET['purchase_id'] ?? false;

        $session = Session::create();
        $session->flash('payment', ['message' => 'Falha no pagamento, tente novamente.', 'type' => 'failure']);

        redirect("/pay?purchase_id={$purchase_id}");
    }

    public function pending()
    {
        $session = Session::create();
        $session->flash('payment', ['message' => 'Pagamento pendente.', 'type' => 'pending']);

        redirect('/order-history');
    }

    public function success()
    {
        $session = Session::create();
        $session->flash('payment', ['message' => 'Pagamento concluído com sucesso.', 'type' => 'success']);

        redirect('/order-history');
    }

    public function qrcode()
    {
        $data = $_GET['data'] ?? false;

        if ($data)
        {
            require ROOT . '/include/qrcode.php';

            $generator = new \QRCode(urldecode($data), ['w' => 400, 'h' => 400, 'wq' => 0]);
            $generator->output_image();
            $image = $generator->render_image();
            imagepng($image);
            imagedestroy($image);
        }
    }

    public function input()
    {
        $data_id = $_REQUEST['data_id'] ?? false;
        $xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        $xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';
        
        if (preg_match('/^ts=(?P<ts>\d+),v1=(?P<hash>\w{64})$/', $xSignature, $matches))
        {
            $pdo = Database::connect();

            $query = 'INSERT INTO mercadopago (data_id, ts, hash) VALUES (:data_id, :ts, :hash)';
            $stmt = $pdo->prepare($query);
            $params = [
                'data_id' => $data_id,
                'ts' => $matches['ts'],
                'hash' => $matches['hash']
            ];
            $stmt->execute($params);

            $config = (require ROOT . '/config.php')['mercadopago'];
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
                
                $response = file_get_contents('https://api.mercadopago.com/v1/payments/' . $data_id, false, $context);
                $data = json_decode($response, true);

                if ($data['status'] != 'approved')
                {
                    exit;
                }

                preg_match('/^US(?P<id>\w{7})$/', $data['external_reference'], $matches);
                $purchase_id = base_convert(strtolower($matches['id']), 36, 10);

                $query = 'UPDATE purchase SET status = :status WHERE id = :id';
                $stmt = $pdo->prepare($query);
                $params = [
                    'id' => $purchase_id,
                    'status' => 'Pagamento aprovado'
                ];
                $stmt->execute($params);
            }
        }
    }
}