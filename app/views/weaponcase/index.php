<?php

$this->layout('layout', [
    'title' => 'UNDEADCASE | Undead Store',
    'description' => 'Teste a sua sorte abrindo uma caixa e ganhe uma linda skin de CS, não precisa de chave.',
    'image' => 'https://undeadstore.com.br/undeadcase.png',
    'session' => $session
]);

?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>

<div class="weaponcases">
    <div class="product">
        <div class="image">
            <img alt="" src="/undeadcase.png" />
        </div>
        <div class="description">
            <div class="title">UNDEADCASE</div>
            <div>Contém um dos seguintes itens:</div>
            <div>
                <div class="rarity rare_weapon">UMP-45 (StatTrak™) | Instrução (Pouco Usada)</div>
                <div class="rarity rare_weapon">MP5-SD (StatTrak™) | Liquefação (Pouco Usada)</div>
                <div class="rarity rare_weapon">Five-SeveN (StatTrak™) | Teste de Chamas (Pouco Usada)</div>
                <div class="rarity mythical_weapon">PP-Bizon (StatTrak™) | Gato Espacial (Pouco Usada)</div>
                <div class="rarity mythical_weapon">P250 (StatTrak™) | Proteção Cibernética (Pouco Usada)</div>
                <div class="rarity mythical_weapon">Galil AR (StatTrak™) | Conexão (Pouco Usada)</div>
                <div class="rarity legendary_weapon">USP-S | Córtex (Testada em Campo)</div>
                <div class="rarity legendary_weapon">XM1014 | BJS (Testada em Campo)</div>
                <div class="rarity legendary_weapon">AUG | Syd Mead (Testada em Campo)</div>
                <div class="rarity ancient_weapon">Glock-18 | Rainha do Chumbo (Testada em Campo)</div>
                <div class="rarity ancient_weapon">M4A4 | Neo-Noir (Testada em Campo)</div>
                <div class="rarity ancient_weapon">AWP (StatTrak™) | Aberração Cromática (Testada em Campo)</div>
            </div>
            <div>*Não precisa de chave para abrir.</div>
            <div class="price">R$ 5,00</div>
            <div>
                <a class="button_buy" href="/cases/buy">Comprar</a>
            </div>
        </div>
    </div>

    <div class="_right weapons">
        <a class="rarity rare_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak™%20UMP-45%20%7C%20Briefing%20(Minimal%20Wear)" target="_blank" title="UMP-45 | Instrução">
            <img alt="" src="/images/ump_45_briefing_fn_mw.png" />
        </a>
        <a class="rarity rare_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak%E2%84%A2%20MP5-SD%20%7C%20Liquidation%20(Minimal%20Wear)" target="_blank" title="MP5-SD | Liquefação">
            <img alt="" src="/images/mp5_sd_liquidation_fn_mw.png" />
        </a>
        <a class="rarity rare_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak%E2%84%A2%20Five-SeveN%20%7C%20Flame%20Test%20(Minimal%20Wear)" target="_blank" title="Five-SeveN | Teste de Chamas">
            <img alt="" src="/images/five_seven_flame_test_fn_mw.png" />
        </a>
        <a class="rarity mythical_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak%E2%84%A2%20PP-Bizon%20%7C%20Space%20Cat%20(Minimal%20Wear)" target="_blank" title="PP-Bizon | Gato Espacial">
            <img alt="" src="/images/pp_bizon_space_cat_fn_mw.png" />
        </a>
        <a class="rarity mythical_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak%E2%84%A2%20P250%20%7C%20Cyber%20Shell%20(Minimal%20Wear)" target="_blank" title="P250 | Proteção Cibernética">
            <img alt="" src="/images/p250_cyber_shell_fn_mw.png" />
        </a>
        <a class="rarity mythical_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak%E2%84%A2%20Galil%20AR%20%7C%20Connexion%20(Minimal%20Wear)" target="_blank" title="Galil AR | Conexão">
            <img alt="" src="/images/galil_ar_connexion_fn_mw.png" />
        </a>
        <a class="rarity legendary_weapon" href="https://steamcommunity.com/market/listings/730/USP-S%20%7C%20Cortex%20(Field-Tested)" target="_blank" title="USP-S | Córtex">
            <img alt="" src="/images/usp_s_cortex_ft_ww.png" />
        </a>
        <a class="rarity legendary_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak%E2%84%A2%20XM1014%20%7C%20XOXO%20(Field-Tested)" target="_blank" title="XM1014 | BJS">
            <img alt="" src="/images/xm1014_xoxo_ft_ww.png" />
        </a>
        <a class="rarity legendary_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak%E2%84%A2%20AUG%20%7C%20Syd%20Mead%20(Field-Tested)" target="_blank" title="AUG | Syd Mead">
            <img alt="" src="/images/aug_syd_mead_ft_ww.png" />
        </a>
        <a class="rarity ancient_weapon" href="https://steamcommunity.com/market/listings/730/Glock-18%20%7C%20Bullet%20Queen%20(Field-Tested)" target="_blank" title="Glock-18 | Rainha do Chumbo">
            <img alt="" src="/images/glock_18_bullet_queen_ft_ww.png" />
        </a>
        <a class="rarity ancient_weapon" href="https://steamcommunity.com/market/listings/730/M4A4%20%7C%20Neo-Noir%20(Field-Tested)" target="_blank" title="M4A4 | Neo-Noir">
            <img alt="" src="/images/m4a4_neo_noir_ft_ww.png" />
        </a>
        <a class="rarity ancient_weapon" href="https://steamcommunity.com/market/listings/730/StatTrak%E2%84%A2%20AWP%20%7C%20Chromatic%20Aberration%20(Field-Tested)" target="_blank" title="AWP | Aberração Cromática">
            <img alt="" src="/images/awp_chromatic_aberration_ft_ww.png" />
        </a>
    </div>

    <div>Probabilidade: Nossa caixa</div>

    <div class="probability _back">
        <div class="rarity rare_weapon" title="Nível Militar">70%</div>
        <div class="rarity mythical_weapon" title="Restrito">20%</div>
        <div class="rarity legendary_weapon" title="Secreto">8%</div>
        <div class="rarity ancient_weapon" title="Oculto">2%</div>
    </div>

    <div>Probabilidade: Caixas no CS 2</div>

    <div class="probability _back">
        <div class="rarity rare_weapon" title="Nível Militar">79,9%</div>
        <div class="rarity mythical_weapon" title="Restrito">15,9%</div>
        <div class="rarity legendary_weapon" title="Secreto">3,2%</div>
        <div class="rarity ancient_weapon" title="Oculto">0,6%</div>
    </div>

    <div class="drop_columns">
        <div class="winners flex column gap-10">
            <div class="user">
                <header>Últimos Drops</header>
            </div>
            <?php foreach ($winners as $item): ?>
            <div class="item">
                <div class="_right">
                    <div class="rarity <?= $item['rarity'] ?>">
                        <img alt="" src="/images/<?= $item['image'] ?>.png" />
                    </div>
                </div>
                <div class="_left">
                    <div class="name"><?= $item['item_name'] ?></div>
                    <div class="flex align-center">
                        <div class="avatar">
                            <img alt="" src="https://avatars.steamstatic.com/<?= $item['avatarhash'] ?>_medium.jpg" />
                        </div>
                        <div class="nickname"><?= $item['personaname'] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
        </div>
        <div class="winners flex column gap-10">
            <div class="user">
                <header>Últimos Melhores Drops</header>
            </div>
            <?php foreach ($best as $item): ?>
            <div class="item">
                <div class="_right">
                    <div class="rarity <?= $item['rarity'] ?>">
                        <img alt="" src="/images/<?= $item['image'] ?>.png" />
                    </div>
                </div>
                <div class="_left">
                    <div class="name"><?= $item['item_name'] ?></div>
                    <div class="flex align-center">
                        <div class="avatar">
                            <img alt="" src="https://avatars.steamstatic.com/<?= $item['avatarhash'] ?>_medium.jpg" />
                        </div>
                        <div class="nickname"><?= $item['personaname'] ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
        </div>
    </div>
</div>