<?php $this->layout('layout', ['title' => 'Suporte | Undead Store', 'session' => $session]) ?>
<div class="flex column">
    <div class="box white">
        <div>Suporte</div>
    </div>
    <div class="box">
        <form action="/support" method="post">
            <div class="flex column gap-20">
                <div>
                    <label for="subject">Assunto</label>
                    <input id="subject" name="subject" placeholder="Assunto" required="required" type="text" />
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
    <div class="box">
        <div>
            <a href="/support/ticket?id=<?= $item['ticket'] ?>">Ticket #<?= $item['ticket'] ?></a>
        </div>
        <div>Assunto: <?= $item['subject'] ?></div>
    </div>
    <?php endforeach ?>
</div>