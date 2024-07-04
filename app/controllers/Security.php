<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;

class Security extends Controller
{
    public function index()
    {
        echo $this->templates->render('security/index');
    }
}