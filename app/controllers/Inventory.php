<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;
use Awesomeundead\Undeadstore\TradeOffer;
use Awesomeundead\Undeadstore\TradeOfferException;

class Inventory extends Controller
{
    public function index()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $user_id = $session->get('user_id');

        $pdo = Database::connect();
        $query = 'SELECT balance FROM wallet WHERE user_id = :user_id';
        $params = ['user_id' => $user_id];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $balance = $stmt->fetchColumn();

        $query = 'SELECT *, inventory.id FROM inventory
                  LEFT JOIN cs_item_variant ON inventory.cs_item_variant_id = cs_item_variant.id
                  LEFT JOIN cs_item ON cs_item_variant.cs_item_id = cs_item.id
                  WHERE user_id = :user_id ORDER BY inventory.id DESC';
        $params = ['user_id' => $user_id];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $listing = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $image_exterior = ['fn'=> 'fn_mw', 'mw'=> 'fn_mw', 'ft'=> 'ft_ww', 'ww'=> 'ft_ww', 'bs'=> 'bs'];

        $exterior = Data::exterior();
        $categories = Data::categories();
        $rarities = Data::rarities();

        echo $this->templates->render('inventory/index', [
            'notification' => $session->flash('trade'),
            'balance' => $balance,
            'listing' => $listing,
            'image_exterior' => $image_exterior,
            'exterior' => $exterior,
            'categories' => $categories,
            'rarities' => $rarities
        ]);
    }

    public function item_sell()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $id = $_GET['id'] ?? 0;
        $user_id = $session->get('user_id');
        $date = date('Y-m-d H:i:s');

        $query = 'SELECT * FROM inventory WHERE id = :id AND user_id = :user_id AND marketable = 1';
        $params = [
            'id' => $id,
            'user_id' => $user_id
        ];

        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$item)
        {
            redirect('/inventory');
        }

        $query = 'SELECT * FROM wallet WHERE user_id = :user_id';
        $params = [
            'user_id' => $user_id
        ];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($wallet)
        {
            $query = 'UPDATE wallet SET balance = :balance WHERE user_id = :user_id';
            $params = [
                'user_id' => $user_id,
                'balance' => (float) $wallet['balance'] + (float) $item['price']
            ];
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
        }
        else
        {
            $query = 'INSERT INTO wallet (user_id, balance) VALUES (:user_id, :balance)';
            $params = [
                'user_id' => $user_id,
                'balance' => $item['price']
            ];
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
        }

        if ($stmt->rowCount())
        {
            $query = 'DELETE FROM inventory WHERE id = :id AND user_id = :user_id';
            $params = [
                'id' => $id,
                'user_id' => $user_id
            ];

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            if ($stmt->rowCount())
            {
                $query = 'UPDATE weaponcase SET quantity = quantity + 1
                          WHERE item_name = :item_name';
                $params = [
                    'item_name' => $item['item_name']
                ];
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);

                // histórico
                $query = 'INSERT INTO inventory_historic (historic_id, user_id, item_name, cs_item_variant_id, status, date)
                          VALUES (:historic_id, :user_id, :item_name, :cs_item_variant_id, :status, :date)';
                $params = [
                    'historic_id' => $item['historic_id'],
                    'user_id' => $user_id,
                    'item_name' => $item['item_name'],
                    'cs_item_variant_id' => $item['cs_item_variant_id'],
                    'status' => 'sold',
                    'date' => $date
                ];

                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
            }
        }

        redirect('/inventory');
    }

    public function item_withdraw()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $id = $_GET['id'] ?? 0;
        $user_id = $session->get('user_id');
        $date = date('Y-m-d H:i:s');

        // Verifica se o item é do usuário e se é trocável
        $query = 'SELECT * FROM inventory WHERE id = :id AND user_id = :user_id AND tradable = 1';
        $params = [
            'id' => $id,
            'user_id' => $user_id
        ];

        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$item)
        {
            redirect('/inventory');
        }

        // Procura o item no estoque
        $query = 'SELECT * FROM weaponcase_stock WHERE cs_item_variant_id = :cs_item_variant_id AND status = :status LIMIT 1';
        $params = [
            'cs_item_variant_id' => $item['cs_item_variant_id'],
            'status' => 'available'
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $stock = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Adiciona o item na lista de trading
        $query = 'INSERT INTO trading (user_id, item_name, steam_asset, status, created_date)
                  VALUES (:user_id, :item_name, :steam_asset, :status, :created_date)';
        $params = [
            'user_id' => $user_id,
            'item_name' => $item['item_name'],
            'steam_asset' => $stock['steam_asset'],
            'status' => 'pending',
            'created_date' => $date
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $trading_id = $pdo->lastInsertId();

        // Atualiza o estoque
        $query = 'UPDATE weaponcase_stock SET status = :status WHERE id = :id';
        $params = [
            'id' => $stock['id'],
            'status' => 'trading'
        ];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        // Apaga o item do inventário
        $query = 'DELETE FROM inventory WHERE id = :id AND user_id = :user_id';
        $params = [
            'id' => $id,
            'user_id' => $user_id
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        // histórico
        $query = 'INSERT INTO inventory_historic (historic_id, user_id, item_name, cs_item_variant_id, steam_asset, trading_id, status, date)
                    VALUES (:historic_id, :user_id, :item_name, :cs_item_variant_id, :steam_asset, :trading_id, :status, :date)';
        $params = [
            'historic_id' => $item['historic_id'],
            'user_id' => $user_id,
            'item_name' => $item['item_name'],
            'cs_item_variant_id' => $item['cs_item_variant_id'],
            'steam_asset' => $stock['steam_asset'],
            'trading_id' => $trading_id,
            'status' => 'trading',
            'date' => $date
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $session->flash('trade', ['message' => 'Em breve uma proposta de troca será enviada.', 'type' => 'success']);

        redirect('/inventory');
    }

    public function weaponcase()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $id = $_GET['id'] ?? 0;
        $user_id = $session->get('user_id');

        $query = 'SELECT * FROM inventory WHERE id = :id AND user_id = :user_id AND item_name = :item_name';
        $params = [
            'id' => $id,
            'user_id' => $user_id,
            'item_name' => 'undeadcase'
        ];

        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$item)
        {
            redirect('/inventory');
        }

        $items_rarity = [
            'rare_weapon'=> [
                'ump_45_briefing_fn_mw',
                'mp5_sd_liquidation_fn_mw',
                'five_seven_flame_test_fn_mw'
            ],
            'mythical_weapon'=> [
                'pp_bizon_space_cat_fn_mw',
                'p250_cyber_shell_fn_mw',
                'galil_ar_connexion_fn_mw'
            ],
            'legendary_weapon'=> [
                'usp_s_cortex_ft_ww',
                'xm1014_xoxo_ft_ww',
                'aug_syd_mead_ft_ww'
            ],
            'ancient_weapon'=> [
                'glock_18_bullet_queen_ft_ww',
                'm4a4_neo_noir_ft_ww',
                'awp_chromatic_aberration_ft_ww'
            ]
        ];

        $rarities = [
            'rare_weapon'      => 4,
            'mythical_weapon'  => 3,
            'legendary_weapon' => 2,
            'ancient_weapon'   => 1
        ];

        echo $this->templates->render('inventory/weaponcase', [
            'item' => $item,
            'items_rarity' => json_encode($items_rarity, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'rarities' => json_encode($rarities)
        ]);
    }

    public function weaponcase_open()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        header('Content-type: application/json; charset=utf-8');

        $id = $_GET['id'] ?? 0;
        $user_id = $session->get('user_id');
        $date = date('Y-m-d H:i:s');

        $query = 'SELECT COUNT(id) FROM inventory
                  WHERE id = :id AND user_id = :user_id AND item_name="undeadcase"';
        $params = [
            'id' => $id,
            'user_id' => $user_id
        ];

        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $item = $stmt->fetchColumn();

        if (!$item)
        {
            redirect('/inventory');
        }

        $query = 'SELECT * FROM weaponcase
                  WHERE case_name = :case_name AND quantity > 0';
        $params = [
            'case_name' => 'undeadcase'
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($list as $value)
        {
            $items[$value['item_code']] = $value;
            $items_rarity[$value['rarity']][$value['item_code']] = $value['chance'];
        }

        if (count($items_rarity) < 4)
        {
            echo json_encode(['error' => '']);

            exit;
        }

        $rarities = [
            'rare_weapon'      => 70,
            'mythical_weapon'  => 20,
            'legendary_weapon' => 8,
            'ancient_weapon'   => 2
        ];
        
        foreach ($rarities as $rarity => $value)
        {
            for ($i = 0; $i < $value; $i++)
            {
                $array[] = $rarity;
            }
        }
        
        shuffle($array); // embaralha o array
        
        $length = count($array); // conta quantos elementos no array, 200
        $random = rand(0, $length - 1); // gera um número aleatório, 0 - 199
        $rarity = $array[$random]; // obtém a raridade do item
        
        $array = [];

        foreach ($items_rarity[$rarity] as $weapon => $value)
        {
            for ($i = 0; $i < $value; $i++)
            {
                $array[] = $weapon;
            }
        }

        shuffle($array);

        $length = count($array);
        $random = rand(0, $length - 1);
        $item = $items[$array[$random]]; // obtém o item

        /*
         * start
         */

        $query = 'UPDATE weaponcase SET quantity = :quantity WHERE id = :id';
        $params = [
            'id' => $item['id'],
            'quantity' => (int) $item['quantity'] - 1
        ];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        /*
         * end
         */

        $query = 'DELETE FROM inventory WHERE id = :id AND user_id = :user_id AND item_name="undeadcase"';
        $params = [
            'id' => $id,
            'user_id' => $user_id
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $query = 'INSERT INTO inventory (historic_id, user_id, item_name, cs_item_variant_id, tradable, marketable, price, created_date)
                  VALUES (:historic_id, :user_id, :item_name, :cs_item_variant_id, :tradable, :marketable, :price, :created_date)';
        $params = [
            'historic_id' => $id,
            'user_id' => $user_id,
            'item_name' => $item['item_name'],
            'cs_item_variant_id' => $item['cs_item_variant_id'],
            'tradable' => $item['tradable'],
            'marketable' => $item['marketable'],
            'price' => $item['price'],
            'created_date' => $date
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        // histórico
        $query = 'INSERT INTO inventory_historic (historic_id, user_id, item_name, status, date)
                  VALUES (:historic_id, :user_id, :item_name, :status, :date)';
        $params = [
            'historic_id' => $id,
            'user_id' => $user_id,
            'item_name' => 'undeadcase',
            'status' => 'open',
            'date' => $date
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        // histórico
        $query = 'INSERT INTO inventory_historic (historic_id, user_id, item_name, cs_item_variant_id, status, date)
                  VALUES (:historic_id, :user_id, :item_name, :cs_item_variant_id, :status, :date)';
        $params = [
            'historic_id' => $id,
            'user_id' => $user_id,
            'item_name' => $item['item_name'],
            'cs_item_variant_id' => $item['cs_item_variant_id'],
            'status' => 'drop',
            'date' => $date
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $data = ['name' => $item['item_name'], 'image' => $item['image']];

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}