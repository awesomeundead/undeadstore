<?php $this->layout('layout', ['title' => 'UNDEADCASE | Undead Store', 'session' => $session]) ?>
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

document.querySelector('form').addEventListener('submit', (e) =>
{
    e.preventDefault();

    fetch('/cases/buy/coins',
    {
        body: new FormData(e.target),
        method: 'post'
    })
    //.then((response) => response.json())
    .then(response =>
        {
            if (response.ok)
            {
                return response.text();
            }

            console.log(response.status);
    })
    .then(text => 
    {
        if (text == '1')
        {
            window.location.replace('/inventory')
        }
        else
        {
            console.log(text);
        }
    });
})
</script>