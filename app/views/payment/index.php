<?php $this->layout('layout', ['title' => 'Pagamento | Undead Store', 'session' => $session]) ?>

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