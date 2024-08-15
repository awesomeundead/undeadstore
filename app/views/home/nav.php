<nav class="listings">
    <div class="menu">
        <a href="/listings?type=agent">Agentes</a>
    </div>
    <div class="menu">
        <button>Escopeta</button>
        <div class="submenu">
            <a href="/listings/shotgun">Todos</a>
            <a href="/listings/shotgun/sawed-off">Cano Curto</a>
            <a href="/listings/shotgun/nova">Nova</a>
            <a href="/listings/shotgun/xm1014">XM1014</a>
        </div>
    </div>
    <!--
    <div class="menu">
        <a href="/listings/machinegun">Metralhadora</a>
    </div>
    -->
    <div class="menu">
        <button>Pistola</button>
        <div class="submenu">
            <a href="/listings/pistol">Todos</a>
            <a href="/listings/pistol/cz75-auto">CZ75-Auto</a>
            <a href="/listings/pistol/desert-eagle">Desert Eagle</a>
            <a href="/listings/pistol/five-seven">Five-SeveN</a>
            <a href="/listings/pistol/glock-18">Glock-18</a>
            <a href="/listings/pistol/p250">P250</a>
            <a href="/listings/pistol/r8-revolver">Revolvér R8</a>    
            <a href="/listings/pistol/usp-s">USP-S</a>
            <a href="/listings/pistol/tec-9">Tec-9</a>
        </div>
    </div>
    <div class="menu">
        <button>Rifle</button>
        <div class="submenu">
            <a href="/listings/rifle">Todos</a>
            <a href="/listings/rifle/ak-47">AK-47</a>
            <a href="/listings/rifle/aug">AUG</a>
            <a href="/listings/rifle/famas">FAMAS</a>
            <a href="/listings/rifle/galil-ar">Galil AR</a>
            <a href="/listings/rifle/m4a1-s">M4A1-S</a>
            <a href="/listings/rifle/m4a4">M4A4</a>
            <a href="/listings/rifle/sg-553">SG 553</a>
        </div>
    </div>
    <div class="menu">
        <button>Rifle de Precisão</button>
        <div class="submenu">
            <a href="/listings/sniper-rifle">Todos</a>
            <a href="/listings/sniper-rifle/awp">AWP</a>
            <a href="/listings/sniper-rifle/ssg-08">SSG 08</a>
        </div>
    </div>
    <div class="menu">
        <button>Submetralhadora</button>
        <div class="submenu">
            <a href="/listings/smg">Todos</a>
            <a href="/listings/smg/mac-10">MAC-10</a>
            <a href="/listings/smg/mp5-sd">MP5-SD</a>
            <a href="/listings/smg/mp7">MP7</a>
            <a href="/listings/smg/mp9">MP9</a>
            <a href="/listings/smg/pp-bizon">PP-Bizon</a>
            <a href="/listings/smg/p90">P90</a>
            <a href="/listings/smg/ump-45">UMP-45</a>
        </div>
    </div>
    <div class="menu">
        <button>Raridade</button>
        <div class="submenu">
            <!--
            <a href="/listings?rarity=Consumer Grade">Nível Consumidor</a>
            <a href="/listings?rarity=Industrial Grade">Nível Industrial</a>
            -->
            <a class="rarity rare_weapon" href="/listings?rarity=rare_weapon">Nível Militar</a>
            <a class="rarity mythical_weapon" href="/listings?rarity=mythical_weapon">Restrito</a>
            <a class="rarity legendary_weapon" href="/listings?rarity=legendary_weapon">Secreto</a>
            <a class="rarity ancient_weapon" href="/listings?rarity=ancient_weapon">Oculto</a>
            <a class="rarity legendary_character" href="/listings?rarity=legendary_character">Superior</a>
            <a class="rarity ancient_character" href="/listings?rarity=ancient_character">Mestre</a>
        </div>
    </div>
    <div class="menu">
        <a href="/cases">UNDEADCASE</a>
    </div>
</nav>

<script>

var clicked;

document.querySelectorAll('nav.listings button').forEach((item, index) =>
{
    item.addEventListener('click', (e) =>
    {
        if (e.target == clicked)
        {
            clicked = null;
            e.target.blur();
        }
        else
        {
            clicked = e.target;
        }
    });
});

document.querySelectorAll('nav.listings a').forEach((item, index) =>
{
    item.addEventListener('click', (e) =>
    {
        clicked = null;
        e.target.blur();
    });
});

</script>