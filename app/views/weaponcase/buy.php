<?php

$this->layout('layout', [
    'title' => 'UNDEADCASE | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<div class="flex column">
    <?php if ($balance >= 5): ?>
        <div class="flex center">
            <a class="button" href="/cases/buy/coins">Comprar com Moedas da Loja</a>
        </div>
    <?php endif ?>
    <div class="box flex column">
        <div class="alert warning">
            <div>Após a confirmação do pagamento, as caixas ficaram no seu <a href="/inventory">Inventário</a>.</div>
            <div>Qualquer dúvida, deixe sua mensagem em <a href="/support">Suporte</a> ou entre no nosso <a href="https://discord.gg/xAe6QYfsCJ" target="_blank">Discord</a>.</div>
        </div>
    </div>
    <div class="box flex align-center" id="quantity_container">
        <label for="quantity">Quantidade</label>
        <select id="quantity">
            <option selected="selected">1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
            <option>7</option>
            <option>8</option>
        </select>
        <div>Total</div>
        <div id="amount">R$ 5,00</div>
    </div>    
    <div class="box white" hidden="hidden" id="mercadopago_alert">Erro interno, tente novamente mais tarde.</div>
    <div id="statusScreenBrick_container"></div>
    <div id="paymentBrick_container"></div>
</div>
<script>

const MP_PUBLIC_KEY = '<?= $mercadopago['public_key'] ?>';
const MP_AMOUNT = <?= $mercadopago['amount'] ?>;
const MP_PAYER = <?= $mercadopago['payer'] ?>;
const MP_PAYMENT_METHODS = <?= $mercadopago['payment_methods'] ?>;
const MP_BACK_URLS = <?= $mercadopago['back_urls'] ?>;

const quantity = document.querySelector('#quantity')

quantity.addEventListener('change', () =>
{
    amount = MP_AMOUNT * quantity.value;

    document.querySelector('#amount').innerHTML = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(amount);

    window.paymentBrickController.update({ amount });
});

</script>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script src="<?= $this->asset('/scripts/mp_weaponcases.js') ?>"></script>