<?php $this->layout('layout', ['title' => 'Undead Store', 'session' => $session]) ?>
<?php $this->insert('home/nav') ?>
<nav>
    <div id="nav_order">
        <a class="order" data-order="asc" href="">Menor preço</a>
        <a class="order" data-order="desc" href="">Maior preço</a>
    </div>
</nav>
<?php $this->insert('home/template') ?>
<script src="/scripts/library.js?release=6"></script>
<script src="/scripts/listings.js?release=8"></script>