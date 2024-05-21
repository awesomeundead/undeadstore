<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Database;

class Listings
{
    private function _get($query, $params = null)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($list);
    }

    public function available()
    {
        $query = 'SELECT *, items.id,
        (CASE 
            WHEN items.type_name = "agent" THEN agents.image
            WHEN items.type_name = "weapon" THEN weapons.image
        END) AS image
        FROM items
        LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
        LEFT JOIN weapons_atrributes ON items.type_id = weapons_atrributes.id AND items.type_name = "weapon"
        LEFT JOIN weapons ON weapons_atrributes.weapon_id = weapons.id
        WHERE availability = :availability
        ORDER BY items.id DESC';

        $params = ['availability' => '1'];

        $this->_get($query, $params);
    }

    public function coming()
    {
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

        $params = ['availability' => '3'];

        $this->_get($query, $params);
    }

    public function family()
    {
        $item = $_GET['item'] ?? false;
        $family = $_GET['family'] ?? false;

        if ($item || $family)
        {
            if ($item == 'agent')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
                WHERE agents.agent_family = :family';
            }
            elseif ($item == 'weapon')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN weapons_atrributes ON items.type_id = weapons_atrributes.id AND items.type_name = "weapon"
                LEFT JOIN weapons ON weapons_atrributes.weapon_id = weapons.id
                WHERE weapons.weapon_family = :family';
            }

            $params = ['family' => $family];
        }
        
        $this->_get($query, $params);
    }

    public function name()
    {
        $item = $_GET['item'] ?? false;
        $name = $_GET['name'] ?? false;

        if ($item || $name)
        {
            if ($item == 'agent')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
                WHERE items.type_name = :agent';
                $params = ['agent' => 'agent'];
            }
            elseif ($item == 'weapon')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN weapons_atrributes ON items.type_id = weapons_atrributes.id AND items.type_name = "weapon"
                LEFT JOIN weapons ON weapons_atrributes.weapon_id = weapons.id
                WHERE weapons.weapon_name = :name';
                $params = ['name' => $name];
            }
        }
        
        $this->_get($query, $params);
    }

    public function rarity()
    {
        $item = $_GET['item'] ?? false;
        $rarity = $_GET['rarity'] ?? false;

        if ($item || $rarity)
        {
            $rarities = [
                'Consumer Grade' => 1,
                'Industrial Grade' => 2,
                'Mil-Spec' => 3,
                'Restricted' => 4,
                'Classified' => 5,
                'Covert' => 6
            ];

            if ($item == 'agent')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
                WHERE items.type_name = :agent AND agents.agent_type = :type';
                $params = ['agent' => 'agent', 'type' => $rarity];
            }
            elseif ($item == 'weapon')
            {
                $rarity = $rarities[$rarity] ?? 0;

                $query = 'SELECT *, items.id FROM items
                LEFT JOIN weapons_atrributes ON items.type_id = weapons_atrributes.id AND items.type_name = "weapon"
                LEFT JOIN weapons ON weapons_atrributes.weapon_id = weapons.id
                WHERE weapons.weapon_rarity = :rarity';
                $params = ['rarity' => $rarity];
            }
        }
        
        $this->_get($query, $params);
    }

    public function type()
    {
        $item = $_GET['item'] ?? false;
        $type = $_GET['type'] ?? false;

        if ($item || $type)
        {
            if ($item == 'agent')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN agents ON items.type_id = agents.id AND items.type_name = "agent"
                WHERE agents.agent_type = :type';
            }
            elseif ($item == 'weapon')
            {
                $query = 'SELECT *, items.id FROM items
                LEFT JOIN weapons_atrributes ON items.type_id = weapons_atrributes.id AND items.type_name = "weapon"
                LEFT JOIN weapons ON weapons_atrributes.weapon_id = weapons.id
                WHERE weapons.weapon_type = :type';
            }

            $params = ['type' => $type];
        }
        
        $this->_get($query, $params);
    }
}