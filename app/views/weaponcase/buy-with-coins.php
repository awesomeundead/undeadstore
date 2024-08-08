<?php $this->layout('layout', ['title' => 'UNDEADCASE | Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<?php if ($balance): ?>
    <div>Saldo da Carteira: <?= $balance ?> Moedas</div>
<?php endif ?>
<form>
    <label for="quantity">Quantidade</label>
    <select id="quantity" name="quantity">
        <?php for ($i = 1; $i <= $quantity; $i++): ?>
            <option><?= $i ?></option>
        <?php endfor ?>
    </select>
    <button type="submit">Comprar</button>
</form>
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