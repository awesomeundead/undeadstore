<?php $this->layout('layout', ['title' => 'Fechar Pedido | Undead Store', 'session' => $session]) ?>
<div class="flex column">
    <?php if ($notification ?? false): ?>
        <div class="box notification <?= $notification['type'] ?>">
            <?= $notification['message'] ?>
        </div>
    <?php endif ?>
    <div class="box header">
        <div>Carrinho de compras</div>
    </div>
    <form action="/checkout/end">
        <div class="flex column">
            <div class="box flex column">
                <div>Selecione uma forma de pagamento disponíveis</div>
                <div class="flex">
                    <input id="pix" name="pay_method" required="required" type="radio" value="pix" />
                    <label class="icon" for="pix">
                        <img alt="Logo do Pix" src="/styles/pix_icon.png" />
                    </label>
                </div>
                <div class="flex align-center wrap">
                    <input id="mercadopago" name="pay_method" required="required" type="radio" value="mercadopago" />
                    <label class="icon" for="mercadopago">
                        <img alt="Logo do Mercado Pago" src="/styles/mercadopago_icon.png" />
                    </label>
                    <div>Este serviço de pagamentos cobra uma tarifa adicional por usar esta forma de pagamento.</div>
                </div>
            </div>
    <div class="box flex column">
        <div class="alert warning">
            <div>Lembre-se de manter sua URL de troca (Trade URL) atualizada, para que possamos enviar o pedido.</div>
            <div>Após a confirmação do pagamento, uma das nossas contas enviará uma proposta de troca. Dependendo do dia pode levar algumas horas.</div>
            <div>Caso não seja identificado o pagamento após 24 horas, o pedido será cancelado.</div>
            <div>Caso aconteça algum problema com os servidores, inventário ou por manutenção do Steam, reembolsamos o pagamento.</div>
            <div>Não fazemos devolução após a transferência dos itens.</div>
            <div>Qualquer dúvida, deixe sua mensagem em <a href="/support">Suporte</a> ou entre no nosso <a href="https://discord.gg/YMvX8g5FhU">Discord</a>.</div>
        </div>
    </div>
    <div class="box white">
        <div class="flex align-center space-between">
            <div>Subtotal</div>
            <div><?= html_money($subtotal) ?></div>
        </div>
        <div class="flex align-center space-between">
            <div>Desconto</div>
            <div><?= html_money($discount) ?></div>
        </div>
        <div class="flex align-center space-between">
            <div>Total</div>
            <div><?= html_money($total) ?></div>
        </div>
    </div>
            <div id="nav_buttons">
                <a href="/cart">Voltar</a>
                <button type="submit">Finalizar pedido</button>
            </div>
        </div>
    </form>
</div>