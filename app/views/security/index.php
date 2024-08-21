<?php

$this->layout('layout', [
    'title' => 'Segurança | Undead Store',
    'description' => 'Skins de Counter-Strike 2 com os melhores preços.',
    'image' => 'https://undeadstore.com.br/styles/undeadstore.png',
    'session' => $session
]);

?>
<style>
figure img
{
    width: 100%;
}
</style>
<div class="flex column gap-10">
    <div class="user">
        <header>Segurança</header>
    </div>
    <div class="box">
        <div>Cuidado com sites falsos, nós nunca pedimos nome de usuário e senha do Steam.</div>
        <figure>
            <picture>
                <source srcset="/official_undeadstore.webp" type="image/webp" />
                <img alt="" src="/official_undeadstore.png" />
            </picture>
            <figcaption>Verifique se o endereço do site é o mesmo da imagem.</figcaption>
        </figure>
    </div>
    <div class="box">
        <div>Para finalizar uma comprar você deve estar logado no Steam.</div>
        <div>O endereço de login do Steam é: https://steamcommunity.com/<br />Sites maliciosos usam endereços parecidos para enganar.</div>
        <figure>
            <picture>
                <source srcset="/official_steam_login.webp" type="image/webp" />
                <img alt="" src="/official_steam_login.png" />
            </picture>
            <figcaption>Se você já estiver logado no Steam, você deverá ver seu nome do Steam.</figcaption>
        </figure>
    </div>
    <div class="box">
        <figure>
            <picture>
                <source srcset="/official_steam_community.webp" type="image/webp" />
                <img alt="" src="/official_steam_community.png" />
            </picture>
            <figcaption>Caso seu navegador não esteja logado no Steam, você deverá ver algo assim. Cuidado, verifique sempre o endereço do site.</figcaption>
        </figure>
    </div>
</div>