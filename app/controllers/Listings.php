<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Database;

class Listings
{
    private function _get($availability)
    {
        header('Content-Type: application/json; charset=utf-8');

        $query = 'SELECT *, items.id,
        (CASE 
            WHEN items.type_name = "agent" THEN agents.image
            WHEN items.type_name = "weapon" THEN weapons.image
        END) AS image
        FROM items
        LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
        LEFT JOIN weapons_atrributes ON items.type_id = weapons_atrributes.id AND items.type_name = "weapon"
        LEFT JOIN weapons ON weapons_atrributes.weapon_id = weapons.id
        WHERE availability = :availability';
        $params = ['availability' => $availability];

        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode($list);
    }

    public function available()
    {
        $this->_get(1);
    }

    public function coming()
    {
        $this->_get(3);
    }

    public function family()
    {
        header('Content-Type: application/json; charset=utf-8');
        
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
                LEFT JOIN weapons_atrributes ON items.type_id = weapons_atrributes.id AND items.type_name = "weapon"
                LEFT JOIN weapons ON weapons_atrributes.weapon_id = weapons.id
                WHERE weapons.weapon_name = :weapon';
                $params = ['weapon' => $parts[1]];
            }
        }
        
        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode($list);
    }
}