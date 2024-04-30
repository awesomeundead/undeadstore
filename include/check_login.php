<?php

require ROOT . '/include/session.php';

$session = Session::create();

if (!$session->get('logged_in'))
{
    redirect('/auth');
}