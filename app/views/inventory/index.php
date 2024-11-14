<?php

$this->layout('layout', [
    'title' => 'Inventário Steam | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'session' => $session
]);

?>
<?php if (!empty($list)) : ?>
<nav>
    <a href="/inventory">Todos</a>
    <a href="/inventory?type=Grafite">Grafite</a>
    <a href="/inventory?type=Recipiente">Recipiente</a>
</nav>
<form action="/inventory/calc" method="post">
    <div class="inventory">
        <?php foreach ($list as $item): ?>
            <div class="item <?= $item['name_color'] ?>">
                <div class="flex space-between">
                    <input id="item_<?= $item['assetid'] ?>" name="item[]" type="checkbox" value="<?= $item['market_hash_name'] ?>;<?= $item['name'] ?>;<?= $item['image'] ?>" />
                    <div class="button">
                        <a href="https://steamcommunity.com/market/listings/730/<?= $item['market_hash_name'] ?>" target="_blank">
                            <img alt="Steam logo" src="/styles/icon_steam_logo.png" />
                        </a>
                    </div>
                </div>
                <div class="image">
                    <label for="item_<?= $item['assetid'] ?>">
                        <img alt="" src="<?= $item['image'] ?>" />
                    </label>
                </div>
                <div>
                    <div class="name"><?= $item['market_name'] ?></div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <button type="submit">Calcular</button>
</form>
<?php endif ?>
<?php if ($error): ?>
    <div><?= $error ?></div>
<?php endif ?>