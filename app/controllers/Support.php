<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Support extends Controller
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
        
        echo $this->templates->render('support/index', [
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name')
            ],
            'list' => $list,
            'notification' => $session->flash('support')
        ]);
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

            $session->flash('support', ['message' => 'Mensagem enviada, responderemos assim que possível.', 'type' => 'success']);

            redirect("/support/ticket?id={$ticket}");
        }

        $session->flash('support', ['message' => 'Não foi possível enviar sua mensagem, tente novamente.', 'type' => 'failure']);

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
        $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$ticket)
        {
            redirect();
        }

        $query = 'SELECT * FROM ticket_items WHERE ticket_id = :ticket_id ORDER BY id DESC';
        $stmt = $pdo->prepare($query);
        $params = [
            'ticket_id' => $ticket['id']
        ];
        $stmt->execute($params);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        echo $this->templates->render('support/ticket', [
            'session' => [
                'loggedin' => $session->get('logged_in'),
                'steam_avatar' => $session->get('steam_avatar'),
                'steam_name' => $session->get('steam_name')
            ],
            'ticket' => $ticket,
            'list' => $list,
            'notification' => $session->flash('support')
        ]);
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
            $session->flash('support', ['message' => 'Mensagem enviada, responderemos assim que possível.', 'type' => 'success']);
        }
        else
        {
            $session->flash('support', ['message' => 'Não foi possível enviar sua mensagem, tente novamente.', 'type' => 'failure']);
        }
        

        redirect("/support/ticket?id={$ticket}");
    }
}