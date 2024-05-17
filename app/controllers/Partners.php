<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Partners extends Controller
{
    public function index()
    {
        $session = Session::create();

        echo $this->templates->render('partners/index', [
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name')
            ]
        ]);
    }
}