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
            'common' => ['en' => 'Base Grade', 'br' => 'Nível Básico'],
            'rare' => ['en' => 'High Grade', 'br' => 'Alta Qualidade'],
            'mythical' => ['en' => 'Remarkable', 'br' => 'Notável'],
            'legendary' => ['en' => 'Exotic', 'br' => 'Exótico'],
            'ancient' => ['en' => 'Extraordinary', 'br' => 'Extraordinário'],
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

    public static function weapon_tag()
    {
        return [
            'ak-47' => 'tag_weapon_ak47',
            'aug' => 'tag_weapon_aug',
            'awp' => 'tag_weapon_awp',
            'bayonet' => 'tag_weapon_bayonet',
            'bowie-knife' => 'tag_weapon_knife_survival_bowie',
            'butterfly-knife' => 'tag_weapon_knife_butterfly',
            'classic-knife' => 'tag_weapon_knife_css',
            'cz75-auto' => 'tag_weapon_cz75a',
            'desert-eagle' => 'tag_weapon_deagle',
            'dual-berettas' => 'tag_weapon_elite',
            'falchion-knife' => 'tag_weapon_knife_falchion',
            'famas' => 'tag_weapon_famas',
            'five-seven' => 'tag_weapon_fiveseven',
            'flip-knife' => 'tag_weapon_knife_flip',
            'g3sg1' => 'tag_weapon_g3sg1',
            'galil-ar' => 'tag_weapon_galilar',
            'glock-18' => 'tag_weapon_glock',
            'gut-knife' => 'tag_weapon_knife_gut',
            'huntsman-knife' => 'tag_weapon_knife_tactical',
            'karambit' => 'tag_weapon_knife_karambit',
            'kukri-knife' => 'tag_weapon_knife_kukri',
            'm249' => 'tag_weapon_m249',
            'm4a1-s' => 'tag_weapon_m4a1_silencer',
            'm4a4' => 'tag_weapon_m4a1',
            'm9-bayonet' => 'tag_weapon_knife_m9_bayonet',
            'mac-10' => 'tag_weapon_mac10',
            'mag-7' => 'tag_weapon_mag7',
            'mp5-sd' => 'tag_weapon_mp5sd',
            'mp7' => 'tag_weapon_mp7',
            'mp9' => 'tag_weapon_mp9',
            'navaja-knife' => 'tag_weapon_knife_gypsy_jackknife',
            'negev' => 'tag_weapon_negev',
            'nomad-knife' => 'tag_weapon_knife_outdoor',
            'nova' => 'tag_weapon_nova',
            'p2000' => 'tag_weapon_hkp2000',
            'p250' => 'tag_weapon_p250',
            'p90' => 'tag_weapon_p90',
            'paracord-knife' => 'tag_weapon_knife_cord',
            'pp-bizon' => 'tag_weapon_bizon',
            'r8-revolver' => 'tag_weapon_revolver',
            'sawed-off' => 'tag_weapon_sawedoff',
            'scar-20' => 'tag_weapon_scar20',
            'sg-553' => 'tag_weapon_sg556',
            'shadow-daggers' => 'tag_weapon_knife_push',
            'skeleton-knife' => 'tag_weapon_knife_skeleton',
            'ssg-08' => 'tag_weapon_ssg08',
            'stiletto-knife' => 'tag_weapon_knife_stiletto',
            'survival-knife' => 'tag_weapon_knife_canis',
            'talon-knife' => 'tag_weapon_knife_widowmaker',
            'tec-9' => 'tag_weapon_tec9',
            'ump-45' => 'tag_weapon_ump45',
            'ursus-knife' => 'tag_weapon_knife_ursus',
            'usp-s' => 'tag_weapon_usp_silencer',
            'xm1014' => 'tag_weapon_xm1014',
            'zeus-x27' => 'tag_weapon_taser'
        ];
    }
}