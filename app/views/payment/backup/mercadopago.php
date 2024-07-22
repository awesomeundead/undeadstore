<?php $this->layout('layout', ['title' => 'Pagamento | Undead Store', 'session' => $session]) ?>
<div class="pay flex column">
    <?php if ($notification ?? false): ?>
        <div class="box notification <?= $notification['type'] ?>">
            <?= $notification['message'] ?>
        </div>
    <?php endif ?>
    
    <div class="box flex column gap-20">
        <div class="">Resumo do pedido</div>
        <div>
            <?php foreach ($description as $item): ?>
                <div><?= $item ?></div>
            <?php endforeach ?>
        </div>
        
        <div>Forma de pagamento</div>
        <div class="flex">
            <div class="box flex white">
                <img alt="Logo do Mercado Pago" src="/styles/mercadopago_icon.png" />
            </div>
        </div>
        
        <div>
            <div>Taxa: <?= html_money($purchase['fee']) ?></div>
            <div>Total a pagar: <?= html_money($purchase['total'] + $purchase['fee']) ?></div>
        </div>
    </div>

    <div class="box flex column align-center white">
        <div id="wallet_container"></div>
    </div>
</div>

<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>

const mp = new MercadoPago('<?= $public_key ?>',
{
    locale: 'pt-BR'
});

mp.bricks().create('wallet', 'wallet_container',
{
    initialization:
    {
        preferenceId: '<?= $preference->id ?>',
    }
});

</script>