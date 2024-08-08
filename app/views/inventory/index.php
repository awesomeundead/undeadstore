<?php $this->layout('layout', ['title' => 'Inventário | Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<?php if ($balance): ?>
    <div>Saldo da Carteira: <?= $balance ?> Moedas</div>
<?php endif ?>
<div class=" header">Inventário</div>
<div class="inventory">
    <?php foreach ($listing as $item): ?>
        <div class="item">
            <?php if ($item['cs_item_variant_id']): ?>
                <div class="rarity <?= $item['rarity'] ?>">
                    <div class="image">
                        <?php if ($item['type'] == 'Agent'): ?>
                            <img alt="" src="/images/<?= $item['image'] ?>.png" />
                        <?php else: ?>
                            <img alt="" src="/images/<?= $item['image'] ?>_<?= $image_exterior[$item['exterior']] ?>.png" />
                        <?php endif ?>
                    </div>
                </div>
                <div class="title">
                    <span><?= $item['name_br'] ?></span>
                    <?php if ($item['category'] && $item['category'] != 'normal'): ?>
                        <span class="category <?= $item['category'] ?>"><?= $categories[$item['category']]['br'] ?></span>
                    <?php endif ?>
                    <span class="family"><?= $item['family_br'] ?></span>
                    <?php if ($item['exterior']): ?>
                        <span>(<?= $exterior[$item['exterior']]['br'] ?>)</span>
                    <?php endif ?>
                </div>
                <?php if ($item['marketable']): ?>
                    <div class="coins"><?= $item['price'] ?> Moedas</div>
                <?php endif ?>
                <div class="flex space-around">
                    <?php if ($item['marketable']): ?>
                        <div class="button">
                            <a class="confirm" href="/inventory/item/sell?id=<?= $item['id'] ?>">Vender</a>
                        </div>
                    <?php endif ?>
                    <div class="button">
                        <a class="confirm" href="/inventory/item/withdraw?id=<?= $item['id'] ?>">Retirar</a>
                    </div>
                </div>
            <?php elseif ($item['item_name'] == 'undeadcase'): ?>
                <div class="image">
                    <a href="/inventory/weaponcase?id=<?= $item['id'] ?>" title="">
                        <img alt="" src="/undeadcase.png" />
                    </a>
                </div>
                <div style="text-transform: uppercase"><?= $item['item_name'] ?></div>
                <div class="button">
                    <a href="/inventory/weaponcase?id=<?= $item['id'] ?>" title="">Abrir</a>
                </div>
            <?php endif ?>
        </div>
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

        document.querySelector('dialog').showModal();

        dialog_corfirm()
        .then(() =>
        {
            window.location.href = element.href;
        })
        .catch(() =>
        {
            document.querySelector('dialog').close();
        });
    });
});

</script>