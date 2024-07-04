<?php $this->layout('layout', ['title' => 'Configurações | Undead Store', 'session' => $session]) ?>
<div class="flex column">
    <div class="box">
        <form action="/settings" method="post">
            <input name="redirect" type="hidden" value="<?= $_GET['redirect'] ?? '' ?>" />
            <div class="flex column gap-20">
                <div>
                    <label for="name">Nome</label>
                    <input id="name" name="name" placeholder="Seu nome" required="required" type="text" value="<?= html_escape($name ?? '') ?>" />
                </div>
                <div>
                    <label for="email">E-mail</label>
                    <input id="email" name="email" placeholder="Seu endereço de e-mail" required="required" type="email" value="<?= html_escape($email ?? '') ?>" />
                    <?php if (!empty($email) && $verified_email == '0'): ?>
                        <div class="alert warning">
                            <div>Endereço de e-mail não verificado.</div>
                            <div>Verifique sua pasta de lixo eletrônico (Spam).</div>
                        </div>
                    <?php endif ?>
                </div>
                <div>
                    <label for="phone">Telefone</label>
                    <input id="phone" name="phone" placeholder="Seu número de telefone com DDD" type="tel" value="<?= html_escape($phone ?? '') ?>" />
                </div>
                <div>
                    <label for="offer_trade">URL de troca (Trade URL)</label>
                    <input id="offer_trade" name="steam_trade_url" placeholder="Sua URL de troca (Trade URL)" required="required" type="text" value="<?= $steam_trade_url ?? '' ?>" />
                    <div class="alert info">
                        <div>
                            Precisamos dessa URL para que possamos enviar o pedido.
                            <a href="https://steamcommunity.com/profiles/<?= $steamid ?>/tradeoffers/privacy#trade_offer_access_url" target="_blank">Clique aqui</a>
                            para obter sua URL de troca (Trade URL).
                        </div>
                    </div>
                </div>
                <div>
                    <button type="submit">Salvar</button>
                </div>
            </div>
        </form>
    </div>
    <?php if ($notification ?? false): ?>
        <div class="box notification <?= $notification['type'] ?>">
            <?= $notification['message'] ?>
        </div>
    <?php endif ?>
</div>