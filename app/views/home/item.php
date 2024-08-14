<?php

if ($item['category'])
{
    if ($item['category'] == 'normal')
    {
        $title = "{$item['name_br']} | {$item['family_br']} ({$exterior[$item['exterior']]['br']})";
    }
    else
    {
        $title = "{$item['name_br']} {$categories[$item['category']]['br']} | {$item['family_br']} ({$exterior[$item['exterior']]['br']})";
    }
}
else
{
    $title = "{$item['name_br']} | {$item['family_br']}";
}

if ($item['exterior'])
{
    $image = "{$item['image']}_{$image_exterior[$item['exterior']]}.png";
}
else
{
    $image = "{$item['image']}.png";
}

$this->layout('layout', [
    'title' => "{$title} | Undead Store",
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => "https://undeadstore.com.br/images/{$image}",
    'session' => $session
]);

?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<nav class="breadcrumb">
    <div><a href="/">Início</a></div>
    <div><a href="/listings?type=<?= $item['type'] ?>"><?= $item['type_br'] ?></a></div>
    <div><a href="/listings?name=<?= $item['name'] ?>"><?= $item['name_br'] ?></a></div>
    <div><a href="/listings?family=<?= $item['family'] ?>"><?= $item['family_br'] ?></a></div>
</nav>
<div class="product">
    <div class="image">
        <?php if ($item['exterior']): ?>
            <a href="">
                <img alt="" src="/images/<?= $item['image'] ?>_<?= $image_exterior[$item['exterior']] ?>.png" />
            </a>
        <?php else: ?>
            <img alt="" src="/images/<?= $item['image'] ?>.png" />
        <?php endif ?>
    </div>
    <div class="description">
        <div class="title">
            <span><?= $item['name_br'] ?></span>
            <?php if ($item['category'] && $item['category'] != 'normal'): ?>
            <span class="category <?= $item['category'] ?>"><?= $categories[$item['category']]['br'] ?></span>
            <?php endif ?>
            <span><?= $item['family_br'] ?></span>
        </div>
        <?php if ($item['pattern_float'] && ($item['availability'] == 1 || $item['availability'] == 3)): ?>
            <div class="attribute pattern">
                <div class="flex space-between">
                    <div>Float</div>
                    <div><?= $item['pattern_float'] ?></div>
                </div>
                <div class="range">
                    <div class="fn" title="Nova de Fábrica"></div>
                    <div class="mw" title="Pouco Usada"></div>
                    <div class="ft" title="Testada em Campo"></div>
                    <div class="ww" title="Bem Desgastada"></div>
                    <div class="bs" title="Veterana de Guerra"></div>
                </div>
                <div class="position">
                    <div class="indicator" style="left: calc(100% * <?= $item['pattern_float'] ?> - 8px)"></div>
                </div>
            </div>
        <?php endif ?>
        <?php if ($item['exterior']): ?>
            <div class="attribute flex space-between">
                <div>Exterior</div>
                <div><?= $exterior[$item['exterior']]['br'] ?></div>
            </div>
        <?php endif ?>
        <div class="attribute flex space-between">
            <div>Tipo</div>
            <div><?= $item['type_br'] ?></div>
        </div>
        <div class="attribute flex space-between">
            <div>Raridade</div>
            <div class="rarity <?= $item['rarity'] ?>">(<?= $rarities[$item['rarity']]['br'] ?>)</div>
        </div>
        <div class="attribute flex space-between">
            <div>Coleção</div>
            <div>
                <a href="/listings?collection=<?= $item['collection_id'] ?>"><?= $item['collection_br'] ?></a>
            </div>
        </div>
        <div class="attribute flex space-between">
            <div>Disponibilidade</div>
            <div><?= $availability[$item['availability']] ?></div>
        </div>
        <div class="market">
            <a href="https://steamcommunity.com/market/listings/730/<?= $item['market_hash_name'] ?>" target="_blank">Mercado Steam</a>
            <?php if ($item['steam_asset'] && $item['availability'] == '1'): ?>
                <a href="https://steamcommunity.com/id/undeadstore/inventory#730_2_<?= $item['steam_asset'] ?>" target="_blank">Ver no inventário</a>
            <?php endif ?>
        </div>
        <?php if ($item['offer_price']): ?>
            <div class="old_price"><?= html_money($item['price']) ?></div>
            <div class="price"><?= html_money($item['offer_price']) ?></div>
            <div class="offer"><?= (float) $item['offer_percentage'] ?>% OFF</div>
        <?php elseif ($item['price']): ?>
            <div class="price"><?= html_money($item['price']) ?></div>
        <?php endif ?>
        <?php if ($item['availability'] == '1'): ?>
            <div>
                <a class="button_buy" href="/cart/add?item_id=<?= $item['id'] ?>">Comprar</a>
            </div>
        <?php endif ?>
    </div>
</div>
<div hidden="hidden" id="expanded">
    <a class="image" href="">
        <img alt="" data-image="/images/<?= $item['image'] ?>.png" src="/styles/loading.svg" />
    </a>
</div>
<script>

const expanded = document.querySelector('#expanded');

expanded.addEventListener('click', (e) =>
{
    e.preventDefault();
    document.body.style.overflow = 'visible';
    expanded.hidden = true;
});

const image = document.querySelector('#expanded img');

document.querySelector('.product .image a').addEventListener('click', (e) => 
{
    e.preventDefault();
    document.body.style.overflow = 'hidden';
    expanded.hidden = false;
    image.src = image.dataset.image;
});

</script>