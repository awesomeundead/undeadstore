<?php

$this->layout('layout', [
    'title' => 'Undead Store | Guia de referência',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>
<?php $this->insert('reference/nav') ?>
<div id="container">
    <div class="items products">
    <?php foreach ($listing as $item): ?>
        <div class="item">
            <div class="title">
                <span class="name"><?= $item['name_br'] ?></span>
                <span class="family"><?= $item['family_br'] ?></span>
                <span class="family">(<?= $item['family'] ?>)</span>
            </div>
            <div class="image">
                <a href="">
                    <img alt="" src="/images/<?= $item['image'] ?>_fn_mw.png" />
                </a>
            </div>
            <div class="attribute-1"><?= $item['type_br'] ?></div>
            <div class="attribute-2 rarity <?= $item['rarity'] ?>"><?= $rarities[$item['rarity']]['br'] ?></div>
            <div class="attribute-1">
                <a href="/reference/collection?id=<?= $item['collection_id'] ?>"><?= $item['collection_br'] ?></a>
            </div>
            <div class="market">
                <a href="https://steamcommunity.com/market/search?category_730_Weapon[]=<?= $categories[html_url($item['name'])] ?>&appid=730&q=<?= $item['family'] ?>" target="_blank">Mercado Steam</a>
            </div>
        </div>
    <?php endforeach ?>
    </div>
</div>