<?php

$input = [
    'data_id'    => $_REQUEST['data_id'] ?? false,
    'xSignature' => $_SERVER['HTTP_X_SIGNATURE'] ?? '',
    'xRequestId' => $_SERVER['HTTP_X_REQUEST_ID'] ?? ''
];

$input = print_r($input, true);

file_put_contents('input.txt', $input, FILE_APPEND);