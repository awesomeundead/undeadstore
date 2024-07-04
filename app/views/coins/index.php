<?php $this->layout('layout', ['title' => 'Moedas de Overwatch 2 | Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<?php $this->insert('home/banner') ?>
<style>
figure img
{
    width: 100%;
}
</style>
<div id="container">
    <div class="item">
        <div class="title">
            <a href="">
                <span class="name">200 Moedas</span>
                <span class="family">Overwatch 2</span>
            </a>
        </div>
        <div class="image">
            <div class="offer"></div>
            <a href="">
                <img alt="" src="/ow_200_coins.png" />
            </a>
        </div>
        <div>
            <a class="button_buy" href="/cart/add?item=ow_200_coins">Comprar</a>
        </div>
        <div class="buy">
            <div class="old_price"></div>
            <div class="price">R$ 9,20</div>
        </div>
    </div>
</div>