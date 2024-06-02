<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;

class Partners extends Controller
{
    public function index()
    {
        echo $this->templates->render('partners/index');
    }
}