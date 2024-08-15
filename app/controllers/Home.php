<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;

class Home extends Controller
{
    public function index()
    {
        echo $this->templates->render('home/index');
    }

    public function listings($type = null, $name = null)
    {
        echo $this->templates->render('home/listings');
    }

    public function item($id, $name)
    {
        $query = 'SELECT *, products.id,
        IF (ISNULL(offer_percentage), NULL, price - (price / 100 * offer_percentage)) AS offer_price
        FROM products
        LEFT JOIN cs_item_variant ON products.cs_item_variant_id = cs_item_variant.id
        LEFT JOIN cs_item ON cs_item_variant.cs_item_id = cs_item.id
        WHERE products.id = :id';
        $params = ['id' => $id];

        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        $query = 'SELECT name AS collection, name_br AS collection_br FROM cs_collections WHERE id = :id';
        $params = ['id' => $item['collection_id']];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $item += $stmt->fetch(\PDO::FETCH_ASSOC);

        $image_exterior = ['fn' => 'fn_mw', 'mw' => 'fn_mw', 'ft' => 'ft_ww', 'ww' => 'ft_ww', 'bs' => 'bs'];

        $exterior = Data::exterior();
        $categories = Data::categories();
        $rarities = Data::rarities();

        $availability = [1 => 'Disponível', 'Sob encomenda', 'Disponível em breve'];

        echo $this->templates->render('home/item', [
            'item' => $item,
            'image_exterior' => $image_exterior,
            'exterior' => $exterior,
            'categories' => $categories,
            'rarities' => $rarities,
            'availability' => $availability
        ]);
    }
}