<nav class="listings">
    <div class="menu">
        <a href="/listings?type=agent">Agentes</a>
    </div>
    <div class="menu">
        <button>Escopeta</button>
        <div class="submenu">
            <a href="/listings?type=Shotgun">Todos</a>
            <a href="/listings?name=Sawed-Off">Cano Curto</a>
            <a href="/listings?name=Nova">Nova</a>
            <a href="/listings?name=XM1014">XM1014</a>
        </div>
    </div>
    <!--
    <div class="menu">
        <a href="/listings?type=Machinegun">Metralhadora</a>
    </div>
    -->
    <div class="menu">
        <button>Pistola</button>
        <div class="submenu">
            <a href="/listings?type=Pistol">Todos</a>
            <a href="/listings?name=CZ75-Auto">CZ75-Auto</a>
            <a href="/listings?name=Desert Eagle">Desert Eagle</a>
            <a href="/listings?name=Five-SeveN">Five-SeveN</a>
            <a href="/listings?name=Glock-18">Glock-18</a>    
            <a href="/listings?name=R8 Revolver">Revolvér R8</a>    
            <a href="/listings?name=USP-S">USP-S</a>
            <a href="/listings?name=Tec-9">Tec-9</a>
        </div>
    </div>
    <div class="menu">
        <button>Rifle</button>
        <div class="submenu">
            <a href="/listings?type=Rifle">Todos</a>
            <a href="/listings?name=AK-47">AK-47</a>
            <a href="/listings?name=AUG">AUG</a>
            <a href="/listings?name=FAMAS">FAMAS</a>
            <a href="/listings?name=Galil AR">Galil AR</a>
            <a href="/listings?name=M4A1-S">M4A1-S</a>
            <a href="/listings?name=M4A4">M4A4</a>
            <a href="/listings?name=SG 553">SG 553</a>
        </div>
    </div>
    <div class="menu">
        <button>Rifle de Precisão</button>
        <div class="submenu">
            <a href="/listings?type=Sniper Rifle">Todos</a>
            <a href="/listings?name=AWP">AWP</a>
            <a href="/listings?name=SSG 08">SSG 08</a>
        </div>
    </div>
    <div class="menu">
        <button>Submetralhadora</button>
        <div class="submenu">
            <a href="/listings?type=SMG">Todos</a>
            <a href="/listings?name=MAC-10">MAC-10</a>
            <a href="/listings?name=MP5-SD">MP5-SD</a>
            <a href="/listings?name=MP9">MP9</a>
            <a href="/listings?name=PP-Bizon">PP-Bizon</a>
            <a href="/listings?name=P90">P90</a>
            <a href="/listings?name=UMP-45">UMP-45</a>
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
</nav>

<script>

list = document.querySelectorAll('nav.listings button');

for (let item of list)
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
}

var clicked;

</script>