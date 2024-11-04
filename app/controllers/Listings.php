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

    public function under()
    {
        $query = 'SELECT cs_item_variant.*, cs_item.*, p.*, p.id,
        IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price,
        IF (ISNULL(offer_percentage), price, price - (price / 100 * offer_percentage)) AS new_price
        FROM products AS p
        LEFT JOIN cs_item_variant ON p.cs_item_variant_id = cs_item_variant.id
        LEFT JOIN cs_item ON cs_item_variant.cs_item_id = cs_item.id
        WHERE (availability = 1 OR availability = 3) AND (price <= :price OR (offer_percentage IS NOT NULL AND price - (price / 100 * offer_percentage) <= :price))
        ORDER BY new_price DESC';

        $params = ['price' => $_GET['price']];

        $this->_get($query, $params);
    }

    public function available()
    {
        $query = 'SELECT cs_item_variant.*, cs_item.*, p.*, p.id,
        IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price
        FROM products AS p
        LEFT JOIN cs_item_variant ON p.cs_item_variant_id = cs_item_variant.id
        LEFT JOIN cs_item ON cs_item_variant.cs_item_id = cs_item.id
        WHERE (availability = 1 OR availability = 3) AND price > 30
        ORDER BY RAND()';

        $this->_get($query);
    }

    public function item()
    {
        $family     = $_GET['family'] ?? false;
        $name       = $_GET['name'] ?? false;
        $rarity     = $_GET['rarity'] ?? false;
        $type       = $_GET['type'] ?? false;
        $collection = $_GET['collection'] ?? false;
        
        if ($family)
        {
            $index = 'family';
            $params = ['value' => $family];
        }
        elseif ($name)
        {
            $index = 'name';
            $name = str_replace('-', '_', $name);
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
            $type = str_replace('-', '_', $type);
            $params = ['value' => $type];
        }
        elseif ($collection)
        {
            $index = 'collection_id';
            $params = ['value' => $collection];
        }
        else
        {
            return null;
        }

        $query = "SELECT cs_item_variant.*, cs_item.*, p.*, p.id,
        IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price
        FROM products AS p
        LEFT JOIN cs_item_variant ON p.cs_item_variant_id = cs_item_variant.id
        LEFT JOIN cs_item ON cs_item_variant.cs_item_id = cs_item.id
        WHERE {$index} LIKE :value
        ORDER BY availability = 1 DESC, availability = 3 DESC";
        
        $this->_get($query, $params);
    }
}