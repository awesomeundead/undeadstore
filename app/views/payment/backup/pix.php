<?php $this->layout('layout', ['title' => 'Pagamento | Undead Store', 'session' => $session]) ?>
<div class="pay box flex column align-center">
    <div class="box flex white">
        <img alt="Logo do Pix" src="/styles/pix_icon.png" />
    </div>
    <div>Use o QR Code para pagar</div>
    <div class="box flex white">
        <img alt="QRCode para pagamento" src="/qrcode?data=<?= $code ?>" />
    </div>
    <div class="total"><?= html_money($purchase['total']) ?></div>
    <button class="clipboard" data-clipboard="<?= $code ?>">Copiar código</button>
</div>
<script>
document.querySelector('.clipboard').addEventListener('click', (e) =>
{
    navigator.clipboard.writeText(e.target.dataset.clipboard);
});
</script>