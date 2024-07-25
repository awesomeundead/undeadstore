<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

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

    private function _send_email($address)
    {
        $config = (require ROOT . '/config.php')['email'];

        $mail = new PHPMailer();

        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->Port       = $config['port'];
        $mail->CharSet    = PHPMailer::CHARSET_UTF8;
    
        $mail->setFrom($config['from']['address'], $config['from']['name']);
        $mail->addAddress($address);
    
        $mail->isHTML(true); 
        $mail->Subject = 'Seu pagamento foi aprovado';
        $mail->Body = "<p>Seu pagamento foi aprovado, agradecemos a sua compra.</p>
        <br />
        <p>Em breve enviaremos uma proposta com os itens para a sua conta do Steam.</p>
        <br />
        <p>Undead Store</p>
        <p>www.undeadstore.com.br</p>";
    
        $mail->send();
    }

    private function send_trade_offer($purchase_id)
    {
        $pdo = Database::connect();

        $query = 'SELECT id FROM purchase WHERE id = :id AND status = :status';
        $params = [
            'id' => $purchase_id,
            'status' => 'approved'
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchColumn();

        if ($result)
        {
            $query = 'SELECT purchase_items.id, steam_asset FROM purchase_items
                      INNER JOIN products ON purchase_items.product_id = products.id
                      WHERE purchase_id = :purchase_id AND status = :status';
            $params = [
                'purchase_id' => $purchase_id,
                'status' => 'pending'
            ];

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($list as $item)
            {
                if ($item['steam_asset'])
                {
                    $assets[] = [
                        'appid' => '730',
                        'contextid' => '2',
                        'amount' => '1',
                        'assetid' => $item['steam_asset']
                    ];

                    $trading[] = $item['id'];
                }
            }

            if ($assets ?? false)
            {
                $query = 'SELECT steamid, steam_trade_url FROM users
                          INNER JOIN purchase ON users.id = purchase.user_id
                          WHERE purchase.id = :id';
                $params = [
                    'id' => $purchase_id
                ];
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($user)
                {
                    $steamID64 = $user['steamid'];
                    $steam_trade_url = $user['steam_trade_url'];

                    require ROOT . '/include/trade.php';
                }
            }

            if ($trading ?? false)
            {
                foreach ($trading as $item)
                {
                    $query = 'UPDATE purchase_items SET status = :status WHERE id = :id';
                    $params = [
                        'id' => $item,
                        'status' => 'trading'
                    ];
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                }
            }
        }
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
            'error' =>  HOST . BASE_PATH . '/payment/update?id=' . $purchase_id,
            'return' =>  HOST . BASE_PATH . '/order-history'
        ];

        $mercadopago['public_key'] = $config['public_key'];
        $mercadopago['amount'] = (float) $purchase['total'];
        $mercadopago['payer'] = json_encode($payer);
        $mercadopago['payment_methods'] = json_encode($payment_methods);
        $mercadopago['payment_id'] = $purchase['payment_id'] ?? 'null';
        $mercadopago['back_urls'] = json_encode($back_urls, JSON_UNESCAPED_SLASHES);
        
        echo $this->templates->render('payment/index', [
            'mercadopago' => $mercadopago,
            'purchase_id' => $purchase_id
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
        $request['external_reference'] = create_external_reference($purchase_id);
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

            echo json_encode($payment);
        }
        catch (MPApiException $e)
        {
            $response = $e->getApiResponse();

            file_put_contents(ROOT . '/log/mercadopago.json', json_encode($response->getContent()), FILE_APPEND);

            echo json_encode($response->getContent());
        }
    }

    /* 
     *  POST REQUEST
     */
    public function notification()
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

            $manifest = "id:{$data_id};request-id:{$xRequestId};ts:{$matches['ts']};";
            $sha = hash_hmac('sha256', $manifest, $config['secret_signature']);

            if ($sha === $matches['hash'])
            {
                MercadoPagoConfig::setAccessToken($config['access_token']);
                //MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

                $client = new PaymentClient();
                $payment = $client->get($data_id);

                if (preg_match('/^US(\d{5})$/', $payment->external_reference, $matches))
                {
                    $purchase_id = $matches[1];
                    $status = ($payment->status == 'approved') ? 'approved' : 'pending';

                    $query = 'UPDATE purchase SET payment_method = :payment_method, payment_id = :payment_id, status = :status WHERE id = :id';
                    $stmt = $pdo->prepare($query);
                    $params = [
                        'id' => $purchase_id,
                        'payment_method' => $payment->payment_method_id,
                        'payment_id' => $payment->id,
                        'status' => $status
                    ];
                    $stmt->execute($params);

                    if ($status == 'approved')
                    {
                        $this->send_trade_offer($purchase_id);

                        $query = 'SELECT email FROM users
                        INNER JOIN purchase ON users.id = purchase.user_id
                        WHERE purchase.id = :id';
                        $stmt = $pdo->prepare($query);
                        $params = [
                            'id' => $purchase_id
                        ];
                        $stmt->execute($params);
                        $email = $stmt->fetchColumn();
                        
                        if (filter_var($email, FILTER_VALIDATE_EMAIL))
                        {
                            $this->_send_email($email);
                        }
                    }
                }
            }
        }
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
        
        $config = (require ROOT . '/config.php')['mercadopago'];

        MercadoPagoConfig::setAccessToken($config['access_token']);
        //MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        $client = new PaymentClient();
        $payment = $client->get($purchase['payment_id']);

        if ($payment->status == 'rejected')
        {
            $pdo = Database::connect();

            $query = 'UPDATE purchase
                    SET status = :status, payment_method = :payment_method, payment_id = :payment_id
                    WHERE id = :id AND user_id = :user_id';
            $stmt = $pdo->prepare($query);
            $params = [
                'id' => $purchase_id,
                'user_id' => $user_id,
                'payment_method' => null,
                'payment_id' => null,
                'status' => 'pending'
            ];
            $stmt->execute($params);
        }

        redirect('/payment?id=' . $purchase_id);
    }
}