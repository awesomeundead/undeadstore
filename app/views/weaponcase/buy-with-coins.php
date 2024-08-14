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
<div class="user">
    <header>UNDEADCASE</header>
    <?php if ($balance): ?>
        <div class="coins">Saldo da Carteira: <span><?= $balance ?> Moedas</span></div>
    <?php endif ?>
</div>
<div class="box flex align-center">
    <div class="image">
        <img alt="" src="/undeadcase.png" />
    </div>
    <form>
        <label for="quantity">Quantidade</label>
        <select id="quantity" name="quantity">
            <?php for ($i = 1; $i <= $quantity; $i++): ?>
                <option><?= $i ?></option>
            <?php endfor ?>
        </select>
        <button type="submit">Comprar</button>
    </form>
</div>
<script>

document.querySelector('form').addEventListener('submit', async e =>
{
    e.preventDefault();

    const submit = document.querySelector('form button[type="submit"]');
    submit.disabled = true;

    try
    {
        const response = await request('/cases/buy/coins', { body: new FormData(e.target), method: 'post' });

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