<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Home extends Controller
{
    public function index()
    {
        echo $this->templates->render('home/index');
    }

    public function listings()
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

        $exterior = [
            'fn' => ['en' => 'Factory New', 'br' => 'Nova de Fábrica'],
            'mw' => ['en' => 'Minimal Wear', 'br' => 'Pouca Usada'],
            'ft' => ['en' => 'Field-Tested', 'br' => 'Testada em Campo'],
            'ww' => ['en' => 'Well Worm', 'br' => 'Bem Desgastada'],
            'bs' => ['en' => 'Battle-Scarred', 'br' => 'Veterana de Guerra']
        ];

        $categories = [
            'normal' => ['en' => 'Normal', 'br' => 'Normal'],
            'tournament' => ['en' => 'Souvenir', 'br' => 'Lembrança'],
            'strange' => ['en' => 'StatTrak™', 'br' => 'StatTrak™'],
            'unusual' => ['en' => '★', 'br' => '★'],
            'unusual_strange' => ['en' => '★ StatTrak™', 'br' => '★ StatTrak™']
        ];

        $rarities = [
            'common' => ['en' => 'Base Grade', 'br' => ''],
            'rare' => ['en' => 'High Grade', 'br' => ''],
            'mythical' => ['en' => 'Remarkable', 'br' => ''],
            'legendary' => ['en' => 'Exotic', 'br' => ''],
            'ancient' => ['en' => 'Extraordinary', 'br' => ''],
            'contraband' => ['en' => 'Contraband', 'br' => 'Contrabando'],
            'common_weapon' => ['en' => 'Consumer Grade', 'br' => 'Nível Consumidor'],
            'uncommon_weapon' => ['en' => 'Industrial Grade', 'br' => 'Nível Industrial'],
            'rare_weapon' => ['en' => 'Mil-Spec', 'br' => 'Nível Militar'],
            'mythical_weapon' => ['en' => 'Restricted', 'br' => 'Restrito'],
            'legendary_weapon' => ['en' => 'Classified', 'br' => 'Secreto'],
            'ancient_weapon' => ['en' => 'Covert', 'br' => 'Oculto'],
            'rare_character' => ['en' => 'Distinguished', 'br' => 'Distinto'],
            'mythical_character' => ['en' => 'Exceptional', 'br' => 'Excepcional'],
            'legendary_character' => ['en' => 'Superior', 'br' => 'Superior'],
            'ancient_character' => ['en' => 'Master', 'br' => 'Mestre']
        ];

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