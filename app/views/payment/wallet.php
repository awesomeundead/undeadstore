<?php

$this->layout('layout', [
    'title' => 'Pagamento | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>

<form>
    <label>Selecione uma forma de pagamento</label>
    <select name="payment_method">
        <option value="wallet">Saldo da carteira</option>
    </select>
    <button class="submit" type="submit">Finalizar</button>
</form>

<script>

const PURCHASE_ID = '<?= $purchase_id ?>';

document.querySelector('form').addEventListener('submit', async e =>
{
    e.preventDefault();

    const submit = document.querySelector('form .submit');
    submit.disabled = true;

    try
    {
        const response = await request('/payment/wallet?id=' + PURCHASE_ID);

        if (response.hasOwnProperty('redirect'))
        {
            window.location.replace(response.redirect);
        }
        else
        {
            submit.disable = false;
        }
    }
    catch (error)
    {
        console.log(error);
    }
});

</script>