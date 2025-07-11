<?php

$this->layout('layout', [
    'title' => 'Pedidos | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>
<div class="flex column">
<?php if ($notification ?? false): ?>
    <div class="box notification <?= $notification['type'] ?>">
        <?= $notification['message'] ?>
    </div>
<?php endif ?>
<div class="user">
    <header>Pedidos</header>
</div>
<?php if ($list): ?>
    <?php foreach ($list as $item): ?>
        <div class="order-history box">
            <header>
                <div class="column">
                    <div class="label">Data</div>
                    <div><?= html_date($item['created_date']) ?></div>
                </div>
                <div class="column">
                    <div class="label">Total</div>
                    <div><?= html_money($item['total']) ?></div>
                </div>
                <div class="column">
                    <div class="label">Status</div>
                    <div><?= $status[$item['status']] ?></div>
                </div>
                <div class="flex column">
                    <div class="label">Pagamento</div>
                    <div>
                        <?php if ($item['payment_method'] == 'wallet'): ?>
                            <span><?= $payment_methods[$item['payment_method']] ?></span>
                        <?php elseif ($item['payment_method']): ?>
                            <a href="/payment?id=<?= $item['id'] ?>"><?= $payment_methods[$item['payment_method']] ?></a>
                        <?php else: ?>
                            <a class="button" href="/payment?id=<?= $item['id'] ?>">Tentar Novamente</a>
                        <?php endif ?>
                    </div>
                </div>
            </header>
            
            <details>
                <summary>Exibir detalhes</summary>
                <div class="flex column">
                    <section>
                        <?php foreach ($item['items'] as $i): ?>
                            <div class="flex align-center space-between">
                                <div class="weapon_full"><?= $i['item_name'] ?></div>
                                <div class="price">
                                <?php if ($i['offer_price'] ?? false): ?>
                                    <div>
                                        <span class="line_through"><?= html_money($i['price']) ?></span>
                                        <span><?= html_money($i['offer_price']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <div><?= html_money($i['price']) ?></div>
                                <?php endif ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </section>
                    
                        <div class="flex align-center space-between">
                            <div class="label">Subtotal</div>
                            <div class="value"><?= html_money($item['subtotal'])  ?></div>
                        </div>
                        <div class="flex align-center space-between">
                            <div class="label">Desconto</div>
                            <div class="value"><?= html_money($item['discount'])  ?></div>
                        </div>
                        <div class="flex align-center space-between">
                            <div class="label">Total</div>
                            <div class="value"><?= html_money($item['total'])  ?></div>
                        </div>
                    
                </div>
            </details>
        </div>
    <?php endforeach ?>
<?php else: ?>
    <div class="box header">
        <div>Você ainda não tem pedidos de compra.</div>
    </div>
<?php endif ?>
</div>