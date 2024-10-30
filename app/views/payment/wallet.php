<?php

$this->layout('layout', [
    'title' => 'Pagamento | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>

<div class="box">
    <form>
        <div class="flex column gap-20">
            <div>
                <label for="payment_method">Selecione uma forma de pagamento</label>
                <div>
                    <select id="payment_method" name="payment_method">
                        <option value="wallet">Saldo da carteira</option>
                    </select>
                </div>
            </div>
            <div>
                <button class="submit" type="submit">Finalizar</button>
            </div>
        </div>
    </form>
</div>

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