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
        $query = 'SELECT *, products.id,
        IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price
        FROM products
        LEFT JOIN items ON products.item_id = items.id
        WHERE availability = :availability || availability = 3
        ORDER BY price DESC';

        $params = ['availability' => '1'];

        $this->_get($query, $params);
    }

    public function coming()
    {
        $query = 'SELECT *, products.id,
        IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price
        FROM products
        LEFT JOIN items ON products.item_id = items.id
        WHERE availability = :availability';

        $params = ['availability' => '3'];

        $this->_get($query, $params);
    }

    public function item()
    {
        $family = $_GET['family'] ?? false;
        $name = $_GET['name'] ?? false;
        $rarity = $_GET['rarity'] ?? false;
        $type = $_GET['type'] ?? false;

        if ($family)
        {
            $index = 'family';
            $params = ['value' => $family];
        }
        elseif ($name)
        {
            $index = 'name';
            $params = ['value' => $name];
        }
        elseif ($rarity)
        {
            $index = 'rarity';
            $params = ['value' => $rarity];
        }
        elseif ($type)
        {
            $index = 'type';
            $params = ['value' => $type];
        }
        else
        {
            $index = 'type';
            $params = ['value' => 'Rifle'];
        }

        $query = "SELECT *, products.id,
        IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price
        FROM products
        LEFT JOIN items ON products.item_id = items.id
        WHERE {$index} = :value
        ORDER BY availability = 1 DESC, availability = 3 DESC";
        
        $this->_get($query, $params);
    }
}