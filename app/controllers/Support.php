<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Support
{
    public function index()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth?redirect=support');
        }

        $pdo = Database::connect();
        $query = 'SELECT * FROM ticket WHERE user_id = :user_id';
        $stmt = $pdo->prepare($query);
        $params = [
            'user_id' => $session->get('user_id')
        ];
        $stmt->execute($params);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $message = $session->flash('support');
        $message_failure = $session->flash('support_failure');
        $content_view = 'support.phtml';

        require VIEW . 'layout.phtml';
    }

    public function create()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth?redirect=support');
        }

        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($subject) || empty($message))
        {
            redirect();
        }

        $ticket = strtoupper(dechex(time()) . '-' . dechex($session->get('user_id')));

        $pdo = Database::connect();
        $query = 'INSERT INTO ticket (user_id, ticket, subject) VALUES (:user_id, :ticket, :subject)';
        $stmt = $pdo->prepare($query);
        $params = [
            'user_id' => $session->get('user_id'),
            'ticket' => $ticket,
            'subject' => $subject
        ];
        $result = $stmt->execute($params);

        if ($result)
        {
            $ticket_id = $pdo->lastInsertId();

            $query = 'INSERT INTO ticket_items (ticket_id, admin, message, created_date) VALUES (:ticket_id, :admin, :message, :created_date)';
            $stmt = $pdo->prepare($query);
            $params = [
                'ticket_id' => $ticket_id,
                'admin' => 0,
                'message' => $message,
                'created_date' => date('Y-m-d H:i:s')
            ];
            $result = $stmt->execute($params);

            $session->flash('support', 'Mensagem enviada, responderemos assim que possível.');

            redirect("/support/ticket?id={$ticket}");
        }

        $session->flash('support_failure', 'Não foi possível enviar sua mensagem, tente novamente.');

        redirect('/support');
    }

    public function ticket()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth?redirect=support');
        }

        $ticket = $_GET['id'] ?? false;

        $pdo = Database::connect();

        $query = 'SELECT * FROM ticket WHERE user_id = :user_id AND ticket = :ticket';
        $stmt = $pdo->prepare($query);
        $params = [
            'user_id' => $session->get('user_id'),
            'ticket' => $ticket
        ];
        $stmt->execute($params);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$item)
        {
            redirect();
        }

        $query = 'SELECT * FROM ticket_items WHERE ticket_id = :ticket_id ORDER BY id DESC';
        $stmt = $pdo->prepare($query);
        $params = [
            'ticket_id' => $item['id']
        ];
        $stmt->execute($params);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $message = $session->flash('support');
        $message_failure = $session->flash('support_failure');
        $content_view = 'support_ticket.phtml';

        require VIEW . 'layout.phtml';
    }

    public function add()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth?redirect=support');
        }

        $ticket = trim($_POST['ticket'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($ticket) || empty($message))
        {
            redirect();
        }

        $pdo = Database::connect();

        $query = 'SELECT * FROM ticket WHERE user_id = :user_id AND ticket = :ticket';
        $stmt = $pdo->prepare($query);
        $params = [
            'user_id' => $session->get('user_id'),
            'ticket' => $ticket
        ];
        $stmt->execute($params);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$item)
        {
            redirect();
        }

        $query = 'INSERT INTO ticket_items (ticket_id, admin, message, created_date) VALUES (:ticket_id, :admin, :message, :created_date)';
        $stmt = $pdo->prepare($query);
        $params = [
            'ticket_id' => $item['id'],
            'admin' => 0,
            'message' => $message,
            'created_date' => date('Y-m-d H:i:s')
        ];
        $result = $stmt->execute($params);

        if ($result)
        {
            $session->flash('support', 'Mensagem enviada, responderemos assim que possível.');
        }
        else
        {
            $session->flash('support_failure', 'Não foi possível enviar sua mensagem, tente novamente.');
        }
        

        redirect("/support/ticket?id={$ticket}");
    }
}