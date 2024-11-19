<?php

$this->layout('layout', [
    'title' => 'Undead Store | Skins de Counter-Strike 2',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>
<?php $this->insert('home/nav') ?>
<h1 style="display: none;">Undead Store | Skins de Counter-Strike 2</h1>
<nav>
    <div id="nav_order">
        <a class="order" data-order="asc" href="">Menor preço</a>
        <a class="order" data-order="desc" href="">Maior preço</a>
        <a class="order" data-order="float" href="">Float</a>
    </div>
</nav>
<?php $this->insert('home/template') ?>
<script src="<?= $this->asset('/scripts/library.js') ?>"></script>
<script src="<?= $this->asset('/scripts/listings.js') ?>"></script>