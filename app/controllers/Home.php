<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Home
{
    public function index()
    {
        $session = Session::create();
        $content_view = 'index.phtml';
        require VIEW . 'layout.phtml';
    }

    public function data()
    {
        $pdo = Database::connect();

        $query = 'SELECT *, items.id,
        (CASE 
            WHEN items.type_name = "agent" THEN agents.image
            WHEN items.type_name = "weapon" THEN weapons.image
        END) AS image
        FROM items
        LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
        LEFT JOIN weapons ON items.type_id = weapons.id AND items.type_name = "weapon"
        WHERE availability = :availability';
        $params = ['availability' => 1];

        $item = $_GET['item'] ?? false;

        if ($item)
        {
            $parts = explode(':', $item);

            if ($parts[0] == 'agent')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
                WHERE items.type_name = :agent';
                $params = ['agent' => 'agent'];
            }
            elseif ($parts[0] == 'weapon')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN weapons ON items.type_id = weapons.id AND items.type_name = "weapon"
                WHERE weapons.weapon_type = :weapon';
                $params = ['weapon' => $parts[1]];
            }
        }
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode($list);
    }
}