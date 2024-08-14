<?php

namespace App\Controllers;

class Data
{
    public static function categories()
    {
        return [
            'normal' => ['en' => 'Normal', 'br' => 'Normal'],
            'tournament' => ['en' => 'Souvenir', 'br' => 'Lembrança'],
            'strange' => ['en' => 'StatTrak™', 'br' => 'StatTrak™'],
            'unusual' => ['en' => '★', 'br' => '★'],
            'unusual_strange' => ['en' => '★ StatTrak™', 'br' => '★ StatTrak™']
        ];
    }

    public static function exterior()
    {
        return [
            'fn' => ['en' => 'Factory New', 'br' => 'Nova de Fábrica'],
            'mw' => ['en' => 'Minimal Wear', 'br' => 'Pouca Usada'],
            'ft' => ['en' => 'Field-Tested', 'br' => 'Testada em Campo'],
            'ww' => ['en' => 'Well Worm', 'br' => 'Bem Desgastada'],
            'bs' => ['en' => 'Battle-Scarred', 'br' => 'Veterana de Guerra']
        ];
    }

    public static function types()
    {
        return [
            ['Agent', 'Agente'],
            ['Machinegun', 'Metralhadora'],
            ['Pistol', 'Pistola'],
            ['Rifle', 'Rifle'],
            ['Shotgun', 'Escopeta'],
            ['SMG', 'Submetralhadora'],
            ['Sniper Rifle', 'Rifle de Precisão']
        ];
    }

    public static function rarities()
    {
        return [
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
    }

    public static function weapons()
    {
        return [
            ['AK-47', 'AK-47'],
            ['AUG', 'AUG'],
            ['AWP', 'AWP'],
            ['CZ75-Auto', 'CZ75-Auto'],
            ['Desert Eagle', 'Desert Eagle'],
            ['Dual Berettas', 'Berettas Duplas'],
            ['FAMAS', 'FAMAS'],
            ['Five-SeveN', 'Five-SeveN'],
            ['Galil AR', 'Galil AR'],
            ['Glock-18', 'Glock-18'],
            ['M4A1-S', 'M4A1-S'],
            ['M4A4', 'M4A4'],
            ['MAC-10', 'MAC-10'],
            ['MP5-SD', 'MP5-SD'],
            ['MP7', 'MP7'],
            ['MP9', 'MP9'],
            ['Nova', 'Nova'],
            ['P250', 'P250'],
            ['P90', 'P90'],
            ['PP-Bizon', 'PP-Bizon'],
            ['R8 Revolver', 'Revólver R8'],
            ['Sawed-Off', 'Cano Curto'],
            ['SG 553', 'SG 553'],
            ['SSG 08', 'SSG 08'],
            ['Tec-9', 'Tec-9'],
            ['UMP-45', 'UMP-45'],
            ['USP-S', 'USP-S'],
            ['XM1014', 'XM1014']
        ];
    }
}