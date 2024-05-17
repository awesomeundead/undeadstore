<?php $this->layout('layout', ['title' => 'Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
    <?php $this->insert('home/banner') ?>

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

<div id="container"></div>
<template>
    <div class="item">
        <div class="title">
            <div>
                <span class="name"></span>
                <span class="stattrak"></span>
            </div>            
            <div class="family"></div>
        </div>
        <div class="image" data-name="">
            <a href="">
                <img alt="" src="/styles/loading.svg" />
            </a>
        </div>
        <div class="attribute-1"></div>
        <div class="attribute-2"></div>
        <div class="market">
            <a href="" target="_blank">Mercado Steam</a>
        </div>
        <div class="availability tag">Sob encomenda</div>
        <div>
            <a class="button_buy" href="">Comprar</a>
        </div>
        <div class="buy">
            <div class="old_price"></div>
            <div class="price"></div>
        </div>
    </div>
</template>
<script src="/scripts/library.js"></script>
<script src="/scripts/listings.js"></script>