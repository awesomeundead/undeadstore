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

        $notification = $session->get('notification');

        if ($notification == 'EMAIL_ADDRESS_NOT_VERIFIED')
        {
            $pdo = Database::connect();

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
        
        $templates->loadExtension(new Asset(ROOT . '/public/'));
        $templates->setDirectory(ROOT . '/app/views');
        $templates->addData([
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name'),
                'notification' => $notification
            ]
        ]);
        
        $this->templates = $templates;
    }
}