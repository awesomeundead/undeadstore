<?php

session_start();

$logged_in = $_SESSION['logged_in'] ?? false;

if ($logged_in)
{
    $steamid = $_SESSION['user']['steamid'];
    $steam_name = $_SESSION['user']['personaname'];
    $steam_avatar = $_SESSION['user']['avatar'];
}

$content_view = 'index.phtml';

require VIEW . 'layout.phtml';