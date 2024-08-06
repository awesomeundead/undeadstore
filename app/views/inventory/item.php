<?php $this->layout('layout', ['title' => 'Inventário | Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<link rel="stylesheet" href="/styles/glide.core.min.css" />
<link rel="preload" as="image" href="/styles/loading.svg">
<div class="opencase">
    <?php if ($item['item_name'] == 'UNDEADCASE'): ?>
        <div class="flex center">
            <img alt="" class="loading" src="/styles/loading.svg" />
        </div>
        <div class="recipient" hidden="hidden">
            <div class="flex center">
                <img alt="" src="/undeadcase.png" />
            </div>
            <div class="_right weapons">
                <div class="rarity rare_weapon" title="UMP-45 | Instrução">
                    <img alt="" src="/images/ump_45_briefing_fn_mw.png" />
                </div>
                <div class="rarity rare_weapon" title="MP5-SD | Liquefação">
                    <img alt="" src="/images/mp5_sd_liquidation_fn_mw.png" />
                </div>
                <div class="rarity rare_weapon" title="Five-SeveN | Teste de Chamas">
                    <img alt="" src="/images/five_seven_flame_test_fn_mw.png" />
                </div>
                <div class="rarity mythical_weapon" title="PP-Bizon | Gato Espacial">
                    <img alt="" src="/images/pp_bizon_space_cat_fn_mw.png" />
                </div>
                <div class="rarity mythical_weapon" title="P250 | Proteção Cibernética">
                    <img alt="" src="/images/p250_cyber_shell_fn_mw.png" />
                </div>
                <div class="rarity mythical_weapon" title="Galil AR | Conexão">
                    <img alt="" src="/images/galil_ar_connexion_fn_mw.png" />
                </div>
                <div class="rarity legendary_weapon" title="USP-S | Córtex">
                    <img alt="" src="/images/usp_s_cortex_ft_ww.png" />
                </div>
                <div class="rarity legendary_weapon" title="XM1014 | BJS">
                    <img alt="" src="/images/xm1014_xoxo_ft_ww.png" />
                </div>
                <div class="rarity legendary_weapon" title="AUG | Syd Mead">
                    <img alt="" src="/images/aug_syd_mead_ft_ww.png" />
                </div>
                <div class="rarity ancient_weapon" title="Glock-18 | Rainha do Chumbo">
                    <img alt="" src="/images/glock_18_bullet_queen_ft_ww.png" />
                </div>
                <div class="rarity ancient_weapon" title="M4A4 | Neo-Noir">
                    <img alt="" src="/images/m4a4_neo_noir_ft_ww.png" />
                </div>
                <div class="rarity ancient_weapon" title="AWP | Aberração Cromática">
                    <img alt="" src="/images/awp_chromatic_aberration_ft_ww.png" />
                </div>
            </div>
            <div>
                <button id="start" type="button">Abrir</button>
            </div>
        </div>
        <div hidden="hidden" id="carousel">
            <div class="layer"></div>
            <div class="glide">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <li class="glide__slide _bottom">
                            <div class="">
                                <img alt="" src="" />
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="indicator"></div>
        </div>
        <div hidden="hidden" class="prize">
            <div class="description"></div>
            <div class="_bottom">
                <div class="rarity">
                    <img alt="" src="" />
                </div>
            </div>
            <div class="flex center">
                <a href="/inventory">Fechar</a>
            </div>
        </div>
    <?php endif ?>
</div>
<script>

const item_id = <?= $item['id'] ?>;

const items_rarity = <?= $items_rarity ?>;

const rarities = <?= $rarities ?>;
    
</script>
<script src="/scripts/glide.min.js"></script>
<script src="/scripts/weaponscase.js"></script>