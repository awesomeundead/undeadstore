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
<?php //$this->insert('home/banner') ?>
<nav class="under">
    <a href="?under=50">Até R$ 50</a>
    <a href="?under=30">Até R$ 30</a>
    <a href="?under=20">Até R$ 20</a>
    <a href="?under=10">Até R$ 10</a>
</nav>
<main>
    <?php $this->insert('home/template') ?>
</main>
<script src="<?= $this->asset('/scripts/library.js') ?>"></script>
<script src="<?= $this->asset('/scripts/index.js') ?>"></script>