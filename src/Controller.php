<?php

namespace Awesomeundead\Undeadstore;

use League\Plates\Engine;

class Controller
{
    protected $templates;

    public function __construct(Engine $templates)
    {
        $session = Session::create();
        
        $templates->setDirectory(ROOT . '/app/views');
        $templates->addData([
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name')
            ]
        ]);
        
        $this->templates = $templates;
    }
}