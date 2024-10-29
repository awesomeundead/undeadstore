<?php

namespace Awesomeundead\Undeadstore;

use League\Plates\Engine;
use League\Plates\Extension\Asset;

class Controller
{
    protected $templates;

    public function __construct(Engine $templates)
    {
        $session = Session::create();
        $pdo = Database::connect();

        $notification = $session->get('notification');

        if ($notification == 'EMAIL_ADDRESS_NOT_VERIFIED')
        {
            $query = 'SELECT verified_email FROM users WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $session->get('user_id')]);
            $verified_email = $stmt->fetchColumn();

            if ($verified_email == '1')
            {
                $session->remove('notification');
                $notification = null;
            }
        }

        $loggedin = $session->get('logged_in');

        if ($loggedin)
        {
            $user_id = $session->get('user_id');
            
            $query = 'SELECT balance FROM wallet WHERE id = :user_id';
            $params = ['user_id' => $user_id];
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $session->set('wallet_balance', $stmt->fetchColumn());
        }
        
        $templates->loadExtension(new Asset(ROOT . '/public/'));
        $templates->setDirectory(ROOT . '/app/views');
        $templates->addData([
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name'),
                'notification' => $notification
            ],
            'wallet_balance' => $session->get('wallet_balance')
        ]);
        
        $this->templates = $templates;
    }
}