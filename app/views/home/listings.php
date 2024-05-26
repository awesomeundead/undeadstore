<?php $this->layout('layout', ['title' => 'Undead Store', 'session' => $session]) ?>
<?php $this->insert('home/nav') ?>
<?php $this->insert('home/banner') ?>
<nav>
    <div id="nav_order">
        <a class="order" data-order="asc" href="">Menor preço</a>
        <a class="order" data-order="desc" href="">Maior preço</a>
    </div>
</nav>
<div hidden="hidden" id="expanded">
    <div class="image">
        <img alt="" src="/styles/loading.svg" />
    </div>
</div>
<?php $this->insert('home/template') ?>
<script src="/scripts/library.js?release=4"></script>
<script src="/scripts/listings.js?release=4"></script>