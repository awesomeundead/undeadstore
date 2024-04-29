var language = 'br';
var item_type;
var order;
var data;

const QUERY_STRING = new URLSearchParams(window.location.search);

window.addEventListener('popstate', async () =>
{
    item_type = new URLSearchParams(window.location.search).get('item');

    await load();     
    reload();
});

document.addEventListener('DOMContentLoaded', async () =>
{
    const nav_weapon_list = document.querySelectorAll('#nav_weapon a');

    for (let item of nav_weapon_list)
    {
        item.addEventListener('click', async (e) =>
        {
            e.preventDefault();
            query = new URL(e.target.href).search;
            item_type = new URLSearchParams(query).get('item');            
            await load();
            reload(); 
            history.pushState(null, null, '?item=' + item_type);
        });
    }

    const nav_list = document.querySelectorAll('#nav_order a');

    for (let item of nav_list)
    {
        item.addEventListener('click', async (e) =>
        {
            e.preventDefault();
            order = e.target.dataset.order;
            reload();
        });
    }

    if (QUERY_STRING.has('item'))
    {
        item_type = QUERY_STRING.get('item');
    }

    await load();
    reload();
});

async function load()
{
    let URL = 'data';

    if (item_type)
    {
        URL = '/data?item=' + item_type;
    }

    try
    {
        const response = await fetch(URL, { method: 'GET' });

        if (response.ok)
        {
            data = await response.json();
            data = Object.values(data);
        }
    }
    catch (error)
    {
        console.log('Erro:' + error.message);
    }
}

function reload()
{
    if (order == 'asc')
    {
        data.sort((a, b) =>
        {
            if (a.price == null)
            {
                return 1;
            }

            if (b.price == null)
            {
                return -1;
            }
            
            return a.price - b.price;
        });
    }
    else if (order == 'desc')
    {
        data.sort((a, b) =>
        {
            return b.price - a.price;
        });
    }

    const node = document.querySelector('template');
    document.querySelector('#container').innerHTML = '';

    for (let key in data)
    {
        template(data[key], node);
    }

    const expanded = document.querySelector('#expanded');

    expanded.addEventListener('click', () =>
    {
        document.body.style.overflow = 'visible';
        expanded.hidden = true;
    });

    const image = document.querySelector('#expanded img');
    list = document.querySelectorAll('.item');
    
    for (let item of list)
    {
        item.querySelector('.image a').addEventListener('click', (e) => 
        {
            e.preventDefault();
            document.body.style.overflow = 'hidden';
            expanded.hidden = false;
            image.src = item.querySelector('.image').dataset.name;
        });
    }
};

function template(item, node)
{
    if (item.availability == 0)
    {
        return;
    }

    const clone = document.importNode(node.content, true);

    if (item.price == null)
    {
        clone.querySelector('.price').remove();
        clone.querySelector('.button_buy').remove();
    }
    else
    {
        clone.querySelector('.button_buy').href = '/cart/add?item_id=' + item.id;

        let price = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.price);

        if (item.offer_price == null)
        {
            clone.querySelector('.old_price').remove();
            clone.querySelector('.price').innerHTML = price;
        }
        else
        {
            let offer_price = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.offer_price);

            clone.querySelector('.old_price').innerHTML = price;
            clone.querySelector('.price').innerHTML = offer_price;
        }        
    }

    if (item.availability == 1)
    {
        clone.querySelector('.availability').innerHTML = 'Disponível';
    }   

    if (item.type_name == 'agent')
    {
        item_agent(item, clone);
    }
    else if (item.type_name == 'weapon')
    {
        item_weapon(item, clone);
    }
    
    document.querySelector('#container').appendChild(clone);
}

function item_agent(item, clone)
{
    const name =
    {
        'en': item.agent_name,
        'br': item.agent_name_br
    }

    const category =
    {
        'en': item.agent_category,
        'br': item.agent_category_br
    }

    url = 'https://steamcommunity.com/market/listings/730/';

    clone.querySelector('.name').innerHTML = category[language];
    clone.querySelector('.weapon').innerHTML = name[language];
    clone.querySelector('.exterior').innerHTML = 'Agente';
    clone.querySelector('.market a').href = url + `${item.agent_name} | ${item.agent_category}`;
    clone.querySelector('.image').dataset.name = `/images/${item.image}.png`;

    const image = clone.querySelector('img');

    new_image = new Image();

    new_image.addEventListener('load', (e) =>
    {        
        image.src = e.target.src;
    });

    new_image.src = `/images/${item.image}.png`;
}

function item_weapon(item, clone)
{
    const exterior = 
    {
        'fn': {'en': 'Factory New', 'br': 'Nova de Fábrica'},
        'mw': {'en': 'Minimal Wear', 'br': 'Pouca Usada'},
        'ft': {'en': 'Field-Tested', 'br': 'Testada em Campo'},
        'ww': {'en': 'Well Worm', 'br': 'Bem Desgastada'},
        'bs': {'en': 'Battle-Scarred', 'br': 'Veterana de Guerra'}
    };

    const type =
    {
        'en': item.weapon_type,
        'br': item.weapon_type_br
    }

    const name =
    {
        'en': item.weapon_name,
        'br': item.weapon_name_br
    }

    url = 'https://steamcommunity.com/market/listings/730/';

    if (item.weapon_stattrak)
    {
        clone.querySelector('.stattrak').innerHTML = '(StatTrak)';
        url += 'StatTrak™ ';
    }

    clone.querySelector('.weapon').innerHTML = type[language];
    clone.querySelector('.name').innerHTML = name[language];
    clone.querySelector('.exterior').innerHTML = exterior[item.weapon_exterior][language];
    clone.querySelector('.market a').href = url + `${item.weapon_type} | ${item.weapon_name} (${exterior[item.weapon_exterior]['en']})`;
    clone.querySelector('.image').dataset.name = `/images/${item.image}.png`;

    const image = clone.querySelector('img');

    new_image = new Image();

    new_image.addEventListener('load', (e) =>
    {        
        image.src = e.target.src;
    });

    new_image.src = `/images/${item.image}_${item.weapon_exterior}.png`;
}