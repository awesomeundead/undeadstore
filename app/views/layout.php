<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="cs, csgo, cs2, counter-strike, skin">
<meta property="og:image" content="https://undeadstore.com.br/logo.png" />
<meta property="og:description" content="Skins de Counter-Strike 2 com os melhores preços." />
<meta property="og:title" content="Undead Store" />
<link href="/favicon.png" rel="icon" type="image/x-icon" />
<link href="/styles/layout.css?release=4" rel="stylesheet" />
<link href="/styles/default.css?release=4" rel="stylesheet" />
<link href="/styles/index.css?release=4" rel="stylesheet" />
<link href="/styles/mobile.css?release=4" rel="stylesheet" />
<title><?= $this->e($title) ?></title>
</head>
<body>

<div id="grid">
    <header id="main_header">
        <div class="left">
            <a href="/">
                <img alt="Logotipo" src="/styles/logo128.png" />
            </a>
        </div>
        <nav class="right">
            <div class="cart_button">
                <a class="image" href="/cart">
                    <img alt="Imagem de um carrinho" src="/styles/cart_icon.png" />
                </a>
            </div>
            <?php if ($session['loggedin']): ?>
                <div id="loggedin">
                    <div class="steam image">
                        <img alt="Avatar" src="<?= $session['steam_avatar'] ?>" />
                        <span><?= $session['steam_name']?></span>
                    </div>
                </div>
            <?php else: ?>
                <div id="login">
                    <a class="image" href="/auth">
                        <span>Entrar</span>
                        <img alt="Steam logo" src="/styles/logo_steam.svg" />
                    </a>
                </div>
            <?php endif ?>
            <?php if ($session['loggedin']): ?>
                <nav>
                    <a href="/settings">Configurações</a>
                    <a href="/order-history">Pedidos</a>
                    <a href="/support">Suporte</a>
                    <a href="/logout">Sair</a>
                </nav>
            <?php endif ?>
        </nav>
    </header>
    <section id="main_section">
        <?= $this->section('content') ?>
    </section>
    <footer id="main_footer">
        <div class="flex_space_between">
            <div class="flex column">
                <div>Links</div>
                <a href="/partners">Parceiros</a>
                <a href="/support">Suporte</a>
            </div>
            <div class="flex column">
                <div>Formas de pagamento</div>
                <div class="flex icons">
                    <div>
                        <img alt="Logo Pix" src="/styles/pix_icon.png" />
                    </div>
                    <div>
                        <img alt="Logo Mercado Pago" src="/styles/mercadopago_icon.png" />
                    </div>
                </div>
                <div class="flex icons">
                    <div>
                        <img alt="Logo Visa" src="/styles/visa_icon.png" />
                    </div>
                    <div>
                        <img alt="Logo Mastercard" src="/styles/mastercard_icon.png" />
                    </div>
                    <div>
                        <img alt="Logo Elo" src="/styles/elo_icon.png" />
                    </div>
                    <div>
                        <img alt="Logo Hipercard" src="/styles/hipercard_icon.png" />
                    </div>
                    <div>
                        <img alt="Logo American Express" src="/styles/amex_icon.png" />
                    </div>
                </div>
            </div>
            <div class="flex column">
                <div>Certificados</div>
                <div class="flex icons">
                    <a href="https://transparencyreport.google.com/safe-browsing/search?url=https:%2F%2Fundeadstore.com.br&hl=pt_BR" target="_blank">
                        <img alt="Logo do Google Safe Browsing" src="/styles/safe_browsing_icon.png" />
                    </a>
                </div>
            </div>
            <div class="flex column">
                <div>Redes sociais</div>
                <div class="social">
                    <a href="https://discord.gg/YMvX8g5FhU" target="_blank">
                        <img alt="Discord logo" src="/styles/icon_discord.png" />
                    </a>
                    <a href="https://steamcommunity.com/groups/undeadstore" target="_blank">
                        <img alt="Steam logo" src="/styles/icon_steam.png" />
                    </a>
                    <a href="https://instagram.com/undeadstore.com.br" target="_blank">
                        <img alt="Instagram logo" src="/styles/icon_instagram.png" />
                    </a>
                </div>
            </div>
        </div>
        <div>
            <div>Undead Store 2024</div>
            <div>Não somos afiliados a Valve.</div>
        </div>
    </footer>
</div>
    
</body>
</html>