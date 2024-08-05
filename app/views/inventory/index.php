<?php $this->layout('layout', ['title' => 'Inventário | Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<div class="inventory">
    <?php foreach ($listing as $item): ?>
        <div class="item">
            <?php if ($item['cs_item_variant_id']): ?>
                <div>
                    <?php if ($item['type'] == 'Agent'): ?>
                        <img alt="" src="/images/<?= $item['image'] ?>.png" width="128px" />
                    <?php else: ?>
                        <img alt="" src="/images/<?= $item['image'] ?>_<?= $image_exterior[$item['exterior']] ?>.png" width="128px" />
                    <?php endif ?>
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
            <?php elseif ($item['item_name'] == 'UNDEADCASE'): ?>
                <div>
                    <a href="/inventory/item?id=<?= $item['id'] ?>">
                        <img alt="" src="/undeadcase.png" width="128px" />
                    </a>
                </div>
                <div><?= $item['item_name'] ?></div>
            <?php endif ?>
        </div>
    <?php endforeach ?>
</div>