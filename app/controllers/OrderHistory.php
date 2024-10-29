<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class OrderHistory extends Controller
{
    public function index()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $pdo = Database::connect();

        $query = 'SELECT * FROM purchase WHERE user_id = :user_id ORDER BY id DESC';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $session->get('user_id')]);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($list as $index => $item)
        {
            $query = 'SELECT * FROM purchase_items WHERE purchase_id = :purchase_id';
            $stmt = $pdo->prepare($query);
            $stmt->execute(['purchase_id' => $item['id']]);
            $list[$index]['items'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $status = ['pending' => 'Pendente', 'approved' => 'Aprovado', 'complete' => 'Concluído', 'canceled' => 'Cancelado'];

        $payment_methods = [
            'pix' => 'PIX',
            'master' => 'Cartão de Crédito',
            'visa' => 'Cartão de Crédito',
            'hypercard' => 'Cartão de Crédito',
            'elo' => 'Cartão de Crédito',
            'amex' => 'Cartão de Crédito',
            'wallet' => 'Carteira'
        ];

        echo $this->templates->render('order-history/index', [
            'list' => $list,
            'status' => $status,
            'payment_methods' => $payment_methods,
            'notification' => $session->flash('payment')
        ]);
    }
}