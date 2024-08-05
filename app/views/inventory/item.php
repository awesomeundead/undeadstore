<?php $this->layout('layout', ['title' => 'Inventário | Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<link rel="stylesheet" href="/styles/glide.core.min.css" />
<div class="">
    <?php if ($item['item_name'] == 'UNDEADCASE'): ?>
        <div class="flex center">
            <div class="sem_nome">
                <div>
                    <div>
                        <img alt="" src="/undeadcase.png" width="256px" />
                    </div>
                    <button id="start" type="button">Abrir</button>
                </div>
                <div>Contém um dos seguintes itens:</div>
                <div>
                    <div class="rarity rare_weapon">UMP-45 (StatTrak™) | Instrução (Pouco Usada)</div>
                    <div class="rarity rare_weapon">MP5-SD (StatTrak™) | Liquefação (Pouco Usada)</div>
                    <div class="rarity rare_weapon">Five-SeveN (StatTrak™) | Teste de Chamas (Pouco Usada)</div>
                    <div class="rarity mythical_weapon">PP-Bizon (StatTrak™) | Gato Espacial (Pouco Usada)</div>
                    <div class="rarity mythical_weapon">P250 (StatTrak™) | Proteção Cibernética (Pouco Usada)</div>
                    <div class="rarity mythical_weapon">Galil AR (StatTrak™) | Conexão (Pouco Usada)</div>
                    <div class="rarity legendary_weapon">USP-S | Córtex (Testada em Campo)</div>
                    <div class="rarity legendary_weapon">XM1014 (StatTrak™) | BJS (Testada em Campo)</div>
                    <div class="rarity legendary_weapon">AUG (StatTrak™) | Syd Mead (Testada em Campo)</div>
                    <div class="rarity ancient_weapon">Glock-18 | Rainha do Chumbo (Testada em Campo)</div>
                    <div class="rarity ancient_weapon">M4A4 | Neo-Noir (Testada em Campo)</div>
                    <div class="rarity ancient_weapon">AWP (StatTrak™) | Aberração Cromática (Testada em Campo)</div>
                </div>
            </div>
            <div class="loading" hidden="hidden">
                <img alt="" src="/styles/loading.svg" />
            </div>
            <div hidden="hidden" id="carousel">
                <div class="layer"></div>
                <div class="glide">
                    <div class="glide__track" data-glide-el="track">
                        <ul class="glide__slides">
                            <li class="glide__slide">
                                <div class="">
                                    <img alt="" src="" />
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="indicator"></div>
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