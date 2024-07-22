<?php $this->layout('layout', ['title' => 'Pagamento | Undead Store', 'session' => $session]) ?>

<div>
    <div id="statusScreenBrick_container"></div>
    <div id="paymentBrick_container"></div>
</div>

<script>

const MP_PUBLIC_KEY = '<?= $mercadopago['public_key'] ?>';
const MP_AMOUNT = <?= $mercadopago['amount'] ?>;
const MP_PAYER = <?= $mercadopago['payer'] ?>;

</script>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script src="/scripts/mercadopago.js"></script>