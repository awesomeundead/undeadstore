<?php

$config = (require ROOT . '/config.php')['pdo'];
$dsn = $config['dsn'];
$username = $config['username'];
$password = $config['password'];

$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);