<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

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

        $query = 'SELECT *, inventory.id FROM inventory
                  LEFT JOIN cs_item_variant ON inventory.cs_item_variant_id = cs_item_variant.id
                  LEFT JOIN cs_item ON cs_item_variant.cs_item_id = cs_item.id
                  WHERE user_id = :user_id AND active = 1';
        $params = ['user_id' => $user_id];

        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $listing = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $image_exterior = ['fn'=> 'fn_mw', 'mw'=> 'fn_mw', 'ft'=> 'ft_ww', 'ww'=> 'ft_ww', 'bs'=> 'bs'];

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

        echo $this->templates->render('inventory/index', [
            'listing' => $listing,
            'image_exterior' => $image_exterior,
            'exterior' => $exterior,
            'categories' => $categories
        ]);
    }

    public function item()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $id = $_GET['id'] ?? 0;

        $user_id = $session->get('user_id');

        $query = 'SELECT *, inventory.id FROM inventory
                  LEFT JOIN cs_item_variant ON inventory.cs_item_variant_id = cs_item_variant.id
                  LEFT JOIN cs_item ON cs_item_variant.cs_item_id = cs_item.id
                  WHERE inventory.id = :id AND user_id = :user_id AND active = 1';
        $params = [
            'id' => $id,
            'user_id' => $user_id
        ];

        $pdo = Database::connect();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

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

        echo $this->templates->render('inventory/item', [
            'item' => $item,
            'items_rarity' => json_encode($items_rarity, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'rarities' => json_encode($rarities)
        ]);
    }

    public function opencase()
    {
        $session = Session::create();

        // Verifica se o usuário está logado
        if (!$session->get('logged_in'))
        {
            redirect('/auth');
        }

        $id = $_GET['id'] ?? 0;

        $user_id = $session->get('user_id');

        $query = 'SELECT COUNT(id) FROM inventory
                  WHERE id = :id AND user_id = :user_id AND item_name="UNDEADCASE" AND active = 1';
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
        
        $items = [
            'ump_45' => [
                'id' => 123,
                'name' => 'UMP-45 (StatTrak™) | Instrução (Pouco Usada)',
                'image' => 'ump_45_briefing_fn_mw'
            ],
            'mp5_sd' => [
                'id' => 124,
                'name' => 'MP5-SD (StatTrak™) | Liquefação (Pouco Usada)',
                'image' => 'mp5_sd_liquidation_fn_mw'
            ],
            'five_seven' => [
                'id' => 125,
                'name' => 'Five-SeveN (StatTrak™) | Teste de Chamas (Pouco Usada)',
                'image' => 'five_seven_flame_test_fn_mw'
            ],
            'pp_bizon' => [
                'id' => 126,
                'name' => 'PP-Bizon (StatTrak™) | Gato Espacial (Pouco Usada)',
                'image' => 'pp_bizon_space_cat_fn_mw'
            ],
            'p250' => [
                'id' => 111,
                'name' => 'P250 (StatTrak™) | Proteção Cibernética (Pouco Usada)',
                'image' => 'p250_cyber_shell_fn_mw'
            ],
            'galil_ar' => [
                'id' => 127,
                'name' => 'Galil AR (StatTrak™) | Conexão (Pouco Usada)',
                'image' => 'galil_ar_connexion_fn_mw'
            ],
            'usp_s' => [
                'id' => 129,
                'name' => 'USP-S | Córtex (Testada em Campo)',
                'image' => 'usp_s_cortex_ft_ww'
            ],
            'xm1014' => [
                'id' => 130,
                'name' => 'XM1014 (StatTrak™) | BJS (Testada em Campo)',
                'image' => 'xm1014_xoxo_ft_ww'
            ],
            'aug' => [
                'id' => 131,
                'name' => 'AUG (StatTrak™) | Syd Mead (Testada em Campo)',
                'image' => 'aug_syd_mead_ft_ww'
            ],
            'glock_18' => [
                'id' => 133,
                'name' => 'Glock-18 | Rainha do Chumbo',
                'image' => 'glock_18_bullet_queen_ft_ww'
            ],
            'm4a4' => [
                'id' => 132,
                'name' => 'M4A4 | Neo-Noir (Testada em Campo)',
                'image' => 'm4a4_neo_noir_ft_ww'
            ],
            'awp' => [
                'id' => 42,
                'name' => 'AWP (StatTrak™) | Aberração Cromática (Testada em Campo)',
                'image' => 'awp_chromatic_aberration_ft_ww'
            ]
        ];

        $rarities = [
            'rare_weapon'      => 70,
            'mythical_weapon'  => 20,
            'legendary_weapon' => 8,
            'ancient_weapon'   => 2
        ];
        
        $items_rarity = [
            'rare_weapon'=> [
                'ump_45',
                'mp5_sd',
                'five_seven'
            ],
            'mythical_weapon'=> [
                'pp_bizon',
                'p250',
                'galil_ar'
            ],
            'legendary_weapon'=> [
                'usp_s',
                'xm1014',
                'aug'
            ],
            'ancient_weapon'=> [
                'glock_18',
                'm4a4',
                'awp'
            ]
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
        
        $length = count($items_rarity[$rarity]); // conta quantos itens existem de acordo com a raridade, 3
        $random = rand(0, $length - 1); // gera um número aleatório, 0 - 2
        $item = $items_rarity[$rarity][$random]; // obtém o item
        
        header('Content-type: application/json; charset=utf-8');

        echo json_encode(['name' => $items[$item]['name'], 'image' => $items[$item]['image']]);
    }
}