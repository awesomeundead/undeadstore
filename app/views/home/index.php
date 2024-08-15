<?php

$this->layout('layout', [
    'title' => 'Undead Store | Skins de Counter-Strike 2',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<?php $this->insert('home/banner') ?>
<?php $this->insert('home/template') ?>
<script src="/scripts/library.js?release=7"></script>
<script src="/scripts/index.js?release=8"></script>