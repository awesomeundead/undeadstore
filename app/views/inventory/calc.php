<?php

$this->layout('layout', [
    'title' => 'Inventário Steam | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'session' => $session
]);

?>
<a href="/inventory">Voltar</a>

<div>Estes valores já estão descontados as taxas do Steam.</div>

<div class="inventory">
    <?php foreach ($rows as $item): ?>
        <div class="item">
            <div class="value"><?= html_money($item['amount']) ?></div>
            <div class="image">
                <img alt="" src="<?= $item['image'] ?>" />
            </div>
            <div class="name"><?= $item['name'] ?></div>
        </div>
    <?php endforeach ?>
</div>
<div class="total">Total <?= html_money($total) ?></div>