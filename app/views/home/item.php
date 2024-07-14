<?php $this->layout('layout', ['title' => 'Undead Store', 'session' => $session]) ?>
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
                <img alt="" src="/images/<?= $item['image'] ?>_<?= $item['exterior'] ?>.png" />
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
        <?php if ($item['exterior']): ?>
            <div class="attribute"><?= $exterior[$item['exterior']]['br'] ?></div>
        <?php endif ?>
        <div class="attribute rarity <?= $item['rarity'] ?>"><?= $item['type_br'] ?> (<?= $rarities[$item['rarity']]['br'] ?>)</div>
        <div class="attribute">Coleção: <?= $item['collection_br'] ?></div>
        <div class="market">
            <a href="https://steamcommunity.com/market/listings/730/<?= $item['market_hash_name'] ?>" target="_blank">Mercado Steam</a>
        </div>
        <div class="attribute availability"><?= $item['availability'] ?></div>
        <?php if ($item['offer_price']): ?>
            <div class="old_price"><?= html_money($item['price']) ?></div>
            <div class="price"><?= html_money($item['offer_price']) ?></div>
            <div class="offer"><?= (float) $item['offer_percentage'] ?>% OFF</div>
        <?php elseif ($item['price']): ?>
            <div class="price"><?= html_money($item['price']) ?></div>
        <?php endif ?>
        <?php if ($item['availability'] == 'Disponível'): ?>
            <a class="button_buy" href="/cart/add?item_id=<?= $item['id'] ?>">Comprar</a>
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