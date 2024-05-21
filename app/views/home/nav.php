<nav class="listings">
    <div class="menu">
        <a href="/listings?item=agent&name=agent">Agentes</a>
    </div>
    <div class="menu">
        <button>Escopeta</button>
        <div class="submenu">
            <a href="/listings?item=weapon&type=Shotgun">Todos</a>
            <a href="/listings?item=weapon&name=Sawed-Off">Cano Curto</a>
            <a href="/listings?item=weapon&name=Nova">Nova</a>
            <a href="/listings?item=weapon&name=XM1014">XM1014</a>
        </div>
    </div>
    <!--
    <div class="menu">
        <a href="/listings?item=weapon&type=Machinegun">Metralhadora</a>
    </div>
    -->
    <div class="menu">
        <button>Pistola</button>
        <div class="submenu">
            <a href="/listings?item=weapon&type=Pistol">Todos</a>
            <a href="/listings?item=weapon&name=CZ75-Auto">CZ75-Auto</a>
            <a href="/listings?item=weapon&name=Desert Eagle">Desert Eagle</a>
            <a href="/listings?item=weapon&name=Five-SeveN">Five-SeveN</a>
            <a href="/listings?item=weapon&name=Glock-18">Glock-18</a>    
            <a href="/listings?item=weapon&name=R8 Revolver">Revolvér R8</a>    
            <a href="/listings?item=weapon&name=USP-S">USP-S</a>
            <a href="/listings?item=weapon&name=Tec-9">Tec-9</a>
        </div>
    </div>
    <div class="menu">
        <button>Rifle</button>
        <div class="submenu">
            <a href="/listings?item=weapon&type=Rifle">Todos</a>
            <a href="/listings?item=weapon&name=AK-47">AK-47</a>
            <a href="/listings?item=weapon&name=AUG">AUG</a>
            <a href="/listings?item=weapon&name=FAMAS">FAMAS</a>
            <a href="/listings?item=weapon&name=Galil AR">Galil AR</a>
            <a href="/listings?item=weapon&name=M4A1-S">M4A1-S</a>
            <a href="/listings?item=weapon&name=M4A4">M4A4</a>
            <a href="/listings?item=weapon&name=SG 553">SG 553</a>
        </div>
    </div>
    <div class="menu">
        <button>Rifle de Precisão</button>
        <div class="submenu">
            <a href="/listings?item=weapon&type=Sniper Rifle">Todos</a>
            <a href="/listings?item=weapon&name=AWP">AWP</a>
            <a href="/listings?item=weapon&name=SSG 08">SSG 08</a>
        </div>
    </div>
    <div class="menu">
        <button>Submetralhadora</button>
        <div class="submenu">
            <a href="/listings?item=weapon&type=SMG">Todos</a>
            <a href="/listings?item=weapon&name=MAC-10">MAC-10</a>
            <a href="/listings?item=weapon&name=MP5-SD">MP5-SD</a>
            <a href="/listings?item=weapon&name=MP9">MP9</a>
            <a href="/listings?item=weapon&name=PP-Bizon">PP-Bizon</a>
            <a href="/listings?item=weapon&name=P90">P90</a>
            <a href="/listings?item=weapon&name=UMP-45">UMP-45</a>
        </div>
    </div>
    <div class="menu">
        <button>Raridade</button>
        <div class="submenu">
            <!--
            <a href="/listings?item=weapon&rarity=Consumer Grade">Nível Consumidor</a>
            <a href="/listings?item=weapon&rarity=Industrial Grade">Nível Industrial</a>
            -->
            <a href="/listings?item=weapon&rarity=Mil-Spec">Nível Militar</a>
            <a href="/listings?item=weapon&rarity=Restricted">Restrito</a>
            <a href="/listings?item=weapon&rarity=Classified">Secreto</a>
            <a href="/listings?item=weapon&rarity=Covert">Oculto</a>
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