<?php $this->layout('layout', ['title' => 'Carrinho | Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<div class="flex column cart">
<?php if ($cart['items'] ?? false): ?>
    <div class="box header">
        <div>Carrinho de compras</div>
    </div>
    <?php foreach ($cart['items'] as $item): ?>
    <div class="box flex align-center space-between">
        <div class="flex align-center space-between">
            <div><img alt="" src="/images/<?= $item['image'] ?>.png" /></div>
            <div>
                <div><?= $item['full_name_br'] ?></div>
                <div class="en"><?= $item['market_hash_name'] ?></div>
            </div>
        </div>
        <div class="flex align-center space-between">
            <div class="delete">
                <a href="/cart/delete?item_id=<?= $item['id'] ?>">
                    <img alt="" src="/styles/delete_icon.png" />
                </a>
            </div>
            <div class="price">
            <?php if ($item['offer_price'] ?? false): ?>
                <div class="line_through"><?= html_money($item['price']) ?></div>    
                <div><?= html_money($item['offer_price']) ?></div>
            <?php else: ?>
                <div><?= html_money($item['price']) ?></div>
            <?php endif ?>
            </div>
        </div>
    </div>
    <?php endforeach ?>
    <?php if ($notification ?? false): ?>
        <div class="box notification <?= $notification['type'] ?>">
            <?= $notification['message'] ?>
        </div>
    <?php endif ?>
    <div class="box flex column">
        <form action="/cart/coupon" method="post">
            <label for="coupon">Cupom</label>
            <div class="flex align-center space-between">
                <input id="coupon" name="coupon" type="text" value="<?= $cart['coupon']['name'] ?? '' ?>" />
                <button type="submit">Adicionar</button>
            </div>
        </form>
    </div>
    <div class="box white">
        <div class="flex align-center space-between">
            <div>Subtotal</div>
            <div><?= html_money($cart['subtotal']) ?></div>
        </div>
        <div class="flex align-center space-between">
            <div>Desconto</div>
            <div>(<?= $cart['percent'] ?>%) <?= html_money($cart['discount']) ?></div>
        </div>
        <div class="flex align-center space-between">
            <div>Total</div>
            <div><?= html_money($cart['total']) ?></div>
        </div>
    </div>
    <div id="nav_buttons">
        <a href="/">Continuar comprando</a>
        <a href="/checkout">Continuar para o pagamento</a>
    </div>
<?php else: ?>
    <div class="box header">
        <div>O seu carrinho está vazio.</div>
    </div>
    <div id="nav_buttons">
        <a href="/">Continuar comprando</a>
    </div>
<?php endif ?>
</div>