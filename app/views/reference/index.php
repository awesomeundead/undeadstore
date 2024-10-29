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
                <a href="/reference/<?= html_url($item['type']) ?>/<?= html_url($item['name']) ?>"><?= $item['name_br'] ?></a>
            </div>
            <div class="image">
                <a href="/reference/<?= html_url($item['type']) ?>/<?= html_url($item['name']) ?>">
                    <img alt="" src="/images/<?= $item['image'] ?>_fn_mw.png" />
                </a>
            </div>
        </div>
    <?php endforeach ?>
    </div>
</div>