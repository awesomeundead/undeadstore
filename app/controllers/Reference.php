<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Reference extends Controller
{
    public function index()
    {
        $pdo = Database::connect();
        
        $query = 'SELECT * FROM cs_item
                  WHERE type NOT IN ("Agent", "Equipment")
                  GROUP BY name';
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $listing = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo $this->templates->render('reference/index', [
            'listing' => $listing
        ]);
    }

    public function listing($type = null, $name = null)
    {
        /*
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/');
        }
        */

        $pdo = Database::connect();
        
        if (isset($name))
        {
            $query = 'SELECT cs_item.*, cs_collections.id AS collection_id, cs_collections.name AS collection, cs_collections.name_br AS collection_br
                      FROM cs_item
                      INNER JOIN cs_collections ON cs_collections.id = cs_item.collection_id
                      WHERE cs_item.name LIKE :name
                      ORDER BY cs_item.family_br';
            
            $item_name = str_replace('-', '_', $name);
            $params = ['name' => $item_name];
        }
        elseif ($type == 'collection')
        {
            $query = 'SELECT cs_item.*, cs_collections.id AS collection_id, cs_collections.name AS collection, cs_collections.name_br AS collection_br
                      FROM cs_item
                      INNER JOIN cs_collections ON cs_collections.id = cs_item.collection_id
                      WHERE cs_collections.id = :id
                      ORDER BY cs_item.family_br';
        
            $params = ['id' => $_GET['id']];
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $listing = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $rarities = Data::rarities();
        $tags = Data::weapon_tag();

        echo $this->templates->render('reference/listing', [
            'listing' => $listing,
            'categories' => $tags,
            'rarities' => $rarities
        ]);
    }
}