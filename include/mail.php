<?php

function send_mail($params)
{
    $message = wordwrap($params['message'], 70, "\r\n");
    $headers = [
        'From' => $params['from'],
        'X-Mailer' => 'PHP/' . phpversion(),
        'Content-Type' => 'text/plain; charset=utf-8'
    ];

    return mail($params['to'], $params['subject'], $message, $headers);
}

$params = [
    'to' => 'daniel.ximenes@outlook.com',
    'from' => 'noreply@undeadstore.com.br'
];