<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Settings
{
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

        $steam_trade_url = $result['steam_trade_url'];
        $name = $result['name'];
        $email = $result['email'];
        $phone = $result['phone'];

        $message = $session->flash('settings');
        $message_failure = $session->flash('settings_failure');

        $content_view = 'settings.phtml';
        $settings_title = 'Configurações';

        require VIEW . 'layout.phtml';
    }

    public function save()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $pdo = Database::connect();

        $steam_trade_url = trim($_POST['steam_trade_url'] ?? '');

        if (!preg_match('#^https://steamcommunity.com/tradeoffer/new/\?partner=(\d+)&token=(\w+)$#', $steam_trade_url, $matches))
        {
            $session->flash('settings_failure', 'URL inválida.');
            redirect('/settings');
        }

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        $query = 'UPDATE users SET steam_trade_url = :steam_trade_url, name = :name, email = :email, phone = :phone WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $params = [
            'id' => $session->get('user_id'),
            'steam_trade_url' => $steam_trade_url,
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ];
        $result = $stmt->execute($params);

        if ($result)
        {
            $session->flash('settings', 'Atualizado com sucesso.');
        }
        else
        {
            $session->flash('settings_failure', 'Ocorreu um erro ao atualizar.');
        }

        redirect('/settings');
    }
}