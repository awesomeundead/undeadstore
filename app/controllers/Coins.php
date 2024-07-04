<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;

class Coins extends Controller
{
    public function index()
    {
        echo $this->templates->render('coins/index');
    }
}