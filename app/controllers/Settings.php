<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Settings extends Controller
{
    private function _send_email($address, $user_id)
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

        $token = hash('sha512', 'coisa_ridicula' . $address);
    
        $mail->isHTML(true); 
        $mail->Subject = 'Verificação de endereço de e-mail';
        $mail->Body = "<p>Para continuar com o cadastro, verifique o endereço de e-mail pelo link abaixo.</p>
        <br />
        <p><a href=\"https://undeadstore.com.br/emailverification?token={$token}&userid={$user_id}\">Verificar endereço de e-mail</a></p>
        <br />
        <p>Adicione nosso endereço de e-mail a lista de contatos confiáveis.</p>
        <br />
        <p>Caso não tenha tentado se cadastrar com este endereço de e-mail recentemente, ignore esta mensagem.</p>";
    
        $mail->send();
    }

    public function index()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $pdo = Database::connect();

        $query = 'SELECT * FROM users WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $session->get('user_id')]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        echo $this->templates->render('settings/index', [
            'steam_trade_url' => $result['steam_trade_url'],
            'steamid' => $session->get('steamid'),
            'name' => $result['name'],
            'email' => $result['email'],
            'verified_email' => $result['verified_email'],
            'phone' => $result['phone'],
            'notification' => $session->flash('settings')
        ]);
    }

    public function email_verification()
    {
        $token = $_GET['token'] ?? false;
        $user_id = $_GET['userid'] ?? false;

        if ($token && $user_id)
        {
            $pdo = Database::connect();

            $query = 'SELECT email FROM users WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $user_id]);
            $email = $stmt->fetchColumn();

            $verify_token = hash('sha512', 'coisa_ridicula' . $email);

            if ($verify_token == $token)
            {
                $query = 'UPDATE users SET verified_email = :verified_email WHERE id = :id';
                $stmt = $pdo->prepare($query);
                $params = [
                    'id' => $user_id,
                    'verified_email' => '1'
                ];
                $result = $stmt->execute($params);
            }
        }

        redirect();
    }

    public function save()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $redirect = $_POST['redirect'] ?? false;

        $user_id = $session->get('user_id');

        $pdo = Database::connect();

        $query = 'SELECT steam_trade_url, name, email, phone FROM users WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $user_id]);
        $user_data = $stmt->fetch(\PDO::FETCH_ASSOC);

        $steam_trade_url = trim($_POST['steam_trade_url'] ?? '');

        if ($steam_trade_url)
        {
            if (!preg_match('#^https://steamcommunity.com/tradeoffer/new/\?partner=(\d+)&token=(\w+)$#', $steam_trade_url, $matches))
            {
                $session->flash('settings', ['message' => 'URL inválida.', 'type' => 'failure']);
                
                if ($redirect == 'checkout')
                {
                    redirect('/settings?redirect=checkout');
                }

                redirect('/settings');
            }
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($user_data['steam_trade_url'] == $steam_trade_url &&
            $user_data['name'] == $name &&
            $user_data['email'] == $email &&
            $user_data['phone'] == $phone)
        {
            if ($redirect == 'checkout')
            {
                redirect('/settings?redirect=checkout');
            }

            redirect('/settings');
        }

        $query = 'UPDATE users SET steam_trade_url = :steam_trade_url, name = :name, phone = :phone WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $params = [
            'id' => $user_id,
            'steam_trade_url' => $steam_trade_url,
            'name' => $name, 
            'phone' => $phone
        ];
        $result = $stmt->execute($params);

        if ($result)
        {
            if ($user_data['email'] != $email)
            {
                $query = 'UPDATE users SET email = :email, verified_email = :verified_email WHERE id = :id';
                $stmt = $pdo->prepare($query);
                $params = [
                    'id' => $user_id,
                    'email' => $email,
                    'verified_email' => '0'
                ];
                $result = $stmt->execute($params);

                if ($result)
                {
                    $session->set('notification', 'EMAIL_ADDRESS_NOT_VERIFIED');

                    $this->_send_email($email, $user_id);
                }
            }

            $session->flash('settings', ['message' => 'Atualizado com sucesso.', 'type' => 'success']);

            if ($redirect == 'checkout')
            {
                redirect('/checkout');
            }
        }
        else
        {
            $session->flash('settings', ['message' => 'Ocorreu um erro ao atualizar.', 'type' => 'failure']);
        }

        if ($redirect == 'checkout')
        {
            redirect('/settings?redirect=checkout');
        }

        redirect('/settings');
    }
}