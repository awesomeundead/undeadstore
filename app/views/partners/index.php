<?php

$this->layout('layout', [
    'title' => 'Parceiros | Undead Store',
    'session' => $session
]);

?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<div class="user">
    <header>Parceiros</header>
</div>
<div class="partners">
    <div class="item">
        <div>fenixpistola</div>
        <div class="image">
            <img alt="" src="/detach/fenixpistola128.png" />
        </div>
        <div class="icons">
            <a href="https://www.twitch.tv/fenixpistola" target="_blank">
                <img alt="Twitch logo" src="/styles/icon_twitch.png" />
            </a>
        </div>
    </div>
    <div class="item">
        <div>dgobode</div>
        <div class="image">
            <img alt="" src="/detach/dgobode128.png" />
        </div>
        <div class="icons">
            <a href="https://www.twitch.tv/dgobode" target="_blank">
                <img alt="Twitch logo" src="/styles/icon_twitch.png" />
            </a>
            <a href="https://www.youtube.com/@dgobode" target="_blank">
                <img alt="YouTube logo" src="/styles/icon_youtube.png" />
            </a>
            <a href="https://www.instagram.com/dgobode" target="_blank">
                <img alt="Instagram logo" src="/styles/icon_instagram.png" />
            </a>
        </div>
    </div>
    <div class="item">
        <div>undeadpistola</div>
        <div class="image">
            <img alt="" src="/detach/undeadpistola128.png" />
        </div>
        <div class="icons">
            <a href="https://www.twitch.tv/undeadpistola" target="_blank">
                <img alt="Twitch logo"src="/styles/icon_twitch.png" />
            </a>
            <a href="https://www.youtube.com/@awesomeundead9342" target="_blank">
                <img alt="YouTube logo"src="/styles/icon_youtube.png" />
            </a>
        </div>
    </div>
</div>