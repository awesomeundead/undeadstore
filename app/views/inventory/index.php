<?php

$this->layout('layout', [
    'title' => 'Inventário | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<div class="flex column">
<?php if ($notification ?? false): ?>
    <div class="box notification <?= $notification['type'] ?>">
        <?= $notification['message'] ?>
    </div>
<?php endif ?>
<div class="user">
    <header>Inventário</header>
    <?php if ($balance): ?>
        <div class="coins">Saldo da Carteira: <span><?= $balance ?> Moedas</span></div>
    <?php endif ?>
    <?php if ($balance >= 5): ?>
        <div class="flex center">
            <a class="button" href="/cases/buy/coins">Comprar Caixas com Moedas da Loja</a>
        </div>
    <?php endif ?>
</div>
<div class="inventory">
    <?php foreach ($listing as $item): ?>
        <div class="item">
            <?php if ($item['cs_item_variant_id']): ?>
                <div class="rarity <?= $item['rarity'] ?>">
                    <div class="image">
                        <a class="dialog" data-item="item_<?= $item['id'] ?>" href="/invetory#item_<?= $item['id'] ?>" title="">
                            <?php if ($item['type'] == 'Agent'): ?>
                                <img alt="" src="/images/<?= $item['image'] ?>.png" />
                            <?php else: ?>
                                <img alt="" src="/images/<?= $item['image'] ?>_<?= $image_exterior[$item['exterior']] ?>.png" />
                            <?php endif ?>
                        </a>
                    </div>
                </div>
                <div class="name"><?= $item['item_name'] ?></div>
            <?php elseif ($item['item_name'] == 'undeadcase'): ?>
                <div class="image">
                    <a href="/inventory/weaponcase?id=<?= $item['id'] ?>" title="">
                        <img alt="" src="/undeadcase.png" />
                    </a>
                </div>
                <div class="name weaponcase"><?= $item['item_name'] ?></div>
            <?php elseif ($item['item_name'] == 'dgobode'): ?>
                <div class="image">
                    <img alt="" src="/dgobode_case.png" />
                </div>
                <div class="name weaponcase"><?= $item['item_name'] ?></div>
            <?php endif ?>
        </div>
        <?php if ($item['cs_item_variant_id']): ?>
            <dialog id="item_<?= $item['id'] ?>">
                <div class="container">                        
                    <div class="flex right">
                        <button class="dialog_close" type="button"></button>
                    </div>

                    <div class="image">
                        <?php if ($item['type'] == 'Agent'): ?>
                            <img alt="" src="/images/<?= $item['image'] ?>.png" />
                        <?php else: ?>
                            <img alt="" src="/images/<?= $item['image'] ?>_<?= $image_exterior[$item['exterior']] ?>.png" />
                        <?php endif ?>
                    </div>
                    

                    <div class="flex column">
                        <div class="title">
                            <span><?= $item['name_br'] ?></span>
                            <?php if ($item['category'] && $item['category'] != 'normal'): ?>
                                <span class="category <?= $item['category'] ?>"><?= $categories[$item['category']]['br'] ?></span>
                            <?php endif ?>
                            <span class="family"><?= $item['family_br'] ?></span>
                        </div>

                        <?php if ($item['marketable']): ?>
                            <div class="attribute flex space-between">
                                <div>Valor</div>
                                <div class="coins"><?= $item['price'] ?> Moedas</div>
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
                        <div class="button">
                            <a href="https://steamcommunity.com/market/listings/730/<?= $item['market_hash_name'] ?>" target="_blank">Mercado Steam</a>
                            <a class="confirm" href="/inventory/item/withdraw?id=<?= $item['id'] ?>">Retirar</a>
                            <?php if ($item['marketable']): ?>
                                <a class="confirm sell" href="/inventory/item/sell?id=<?= $item['id'] ?>">Vender</a>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </dialog>
        <?php endif ?>
    <?php endforeach ?>
</div>
<dialog class="confirm">
    <div class="container">
        <div>Você tem certeza?</div>
        <button type="button" value="1">Sim</button>
        <button type="button" value="0">Não</button>
    </div>
</dialog>
<script>

const dialog_corfirm = () => new Promise((resolve, reject) =>
{
    document.querySelector('dialog button[value="1"]').addEventListener('click', resolve);
    document.querySelector('dialog button[value="0"]').addEventListener('click', reject);
});

document.querySelectorAll('a.confirm').forEach((element) =>
{
    element.addEventListener('click', e =>
    {
        e.preventDefault();

        document.querySelector('dialog.confirm').showModal();

        dialog_corfirm()
        .then(() =>
        {
            window.location.href = element.href;
        })
        .catch(() =>
        {
            document.querySelector('dialog.confirm').close();
        });
    });
});

document.querySelectorAll('a.dialog').forEach((element) =>
{
    element.addEventListener('click', e =>
    {
        e.preventDefault();

        dialog_id = element.dataset.item;

        document.querySelector('dialog#' + dialog_id).showModal();
    });
});

document.querySelectorAll('.dialog_close').forEach((element) =>
{
    element.addEventListener('click', () =>
    {
        document.querySelector('dialog#' + dialog_id).close();
    });
});

var dialog_id;

</script>