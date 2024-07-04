<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_mail($params)
{
    $mail = new PHPMailer();

    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreply@undeadstore.com.br';
    $mail->Password   = 'ed8f1new$TO';
    $mail->Port       = 587;
    $mail->CharSet    = PHPMailer::CHARSET_UTF8;

    $mail->setFrom('noreply@undeadstore.com.br', 'Undead Store');
    $mail->addAddress('daniel.ximenes@outlook.com', 'Undead');

    $mail->Subject = $params['subject'];
    $mail->Body = $params['message'];

    $mail->send();
}