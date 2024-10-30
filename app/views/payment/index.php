<?php

$this->layout('layout', [
    'title' => 'Pagamento | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>

<div class="box">
    <div class="flex space-between">
        <div>Valor da compra:</div>
        <div><?= html_money($purchase_total) ?></div>
    </div>
    <div id="wallet_container">
        <?php if ($wallet_balance > 0): ?>
            <div class="flex space-between">
                <div>Saldo da carteira:</div>
                <div><?= html_money($wallet_balance) ?></div>
            </div>
            <div class="flex space-between">
                <div>Valor restante:</div>
                <div><?= html_money($remaining) ?></div>
            </div>
        <?php endif ?>
    </div>
</div>

<div class="flex column">
    <div class="box white" hidden="hidden" id="mercadopago_alert">Erro interno, tente novamente mais tarde.</div>
    <div id="statusScreenBrick_container"></div>
    <div id="paymentBrick_container"></div>
</div>

<script>

const PURCHASE_ID = '<?= $purchase_id ?>';
const MP_PUBLIC_KEY = '<?= $mercadopago['public_key'] ?>';
const MP_AMOUNT = <?= $mercadopago['amount'] ?>;
const MP_PAYER = <?= $mercadopago['payer'] ?>;
const MP_PAYMENT_METHODS = <?= $mercadopago['payment_methods'] ?>;
const MP_PAYMENT_ID =  <?= $mercadopago['payment_id'] ?>;
const MP_BACK_URLS = <?= $mercadopago['back_urls'] ?>;

</script>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script src="/scripts/mercadopago.js"></script>