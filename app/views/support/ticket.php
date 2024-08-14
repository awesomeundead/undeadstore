<?php

$this->layout('layout', [
    'title' => 'Suporte | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>
<div class="flex column">
    <div class="box white">
        <div>Ticket #<?= $ticket['ticket'] ?></div>
    </div>
    <div class="box">
        <form action="/support/ticket" method="post">
            <input name="ticket" type="hidden" value="<?= $ticket['ticket'] ?>" />
            <div class="flex column gap-20">
                <div>
                    <label for="subject">Assunto</label>
                    <input disabled="disabled" id="subject" type="text" value="<?= html_escape($ticket['subject']) ?>" />
                </div>
                <div>
                    <label for="message">Mensagem</label>
                    <textarea id="message" name="message" placeholder="Mensagem" required="required" rows="6"></textarea>
                </div>
                <div>
                    <button type="submit">Enviar</button>
                </div>
            </div>
        </form>
    </div>
    <?php if ($notification ?? false): ?>
        <div class="box notification <?= $notification['type'] ?>">
            <?= $notification['message'] ?>
        </div>
    <?php endif ?>
    <?php foreach ($list as $item): ?>
        <?php if ($item['admin']): ?>
        <div class="box white">        
            <div><?= html_date($item['created_date']) ?></div>
            <div><?= html_escape($item['message']) ?></div>
        </div>
        <?php else: ?>
        <div class="box">        
            <div><?= html_date($item['created_date']) ?></div>
            <div><?= html_escape($item['message']) ?></div>
        </div>
        <?php endif ?>
    <?php endforeach ?>
</div>