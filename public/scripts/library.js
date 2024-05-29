request = async (url) =>
{
    const response = await fetch(url);

    if (response.ok)
    {
        if (response.headers.get('Content-Type').includes('application/json'))
        {   
            return await response.json();
        }
    }

    throw new Error('NOT FOUND');
}

create = (data, fragment, container) =>
{
    container.innerHTML = '';

    for (let key in data)
    {
        template(data[key], fragment, container);
    }

    const expanded = document.querySelector('#expanded');

    expanded.addEventListener('click', () =>
    {
        document.body.style.overflow = 'visible';
        expanded.hidden = true;
    });

    const image = document.querySelector('#expanded img');
    let list = document.querySelectorAll('.item');
    
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
}

function template(item, fragment, container)
{
    if (item.availability == 0)
    {
        return;
    }

    const clone = document.importNode(fragment.content, true);   
    
    clone.querySelector('.availability').innerHTML = AVAILABILITY.status[item.availability];
    
    if (item.availability == AVAILABILITY.ORDER)
    {
        clone.querySelector('.offer').remove();
        clone.querySelector('.price').remove();
        clone.querySelector('.button_buy').remove();
    }
    else
    {
        if (item.price != null)
        {
            let price = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.price);

            if (item.offer_price == null)
            {
                clone.querySelector('.offer').remove();
                clone.querySelector('.old_price').remove();
                clone.querySelector('.price').innerHTML = price;
            }
            else
            {
                let offer_price = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.offer_price);
                let percentage = parseFloat(item.offer_percentage);

                clone.querySelector('.offer').innerHTML = `${percentage}% OFF`;
                clone.querySelector('.old_price').innerHTML = price;
                clone.querySelector('.price').innerHTML = offer_price;
            }
        }
        else
        {
            clone.querySelector('.offer').remove();
            clone.querySelector('.price').remove();
        }

        if (item.availability == AVAILABILITY.AVAILABLE)
        {
            if (item.price != null)
            {
                clone.querySelector('.button_buy').href = '/cart/add?item_id=' + item.id;
            }
            else
            {
                clone.querySelector('.button_buy').remove();
            }
        }
        else if (item.availability == AVAILABILITY.COMING_SOON)
        {
            clone.querySelector('.button_buy').remove();
            clone.querySelector('.availability').classList.add('soon');
        }
    }

    if (item.type_name == 'agent')
    {
        item_agent(item, clone);
    }
    else if (item.type_name == 'weapon')
    {
        item_weapon(item, clone);
    }
    
    container.appendChild(clone);
}

function item_agent(item, clone)
{
    const type =
    {
        'en': item.agent_type,
        'br': item.agent_type_br
    }

    const name =
    {
        'en': item.agent_name,
        'br': item.agent_name_br
    }

    const family =
    {
        'en': item.agent_family,
        'br': item.agent_family_br
    }

    clone.querySelector('.family a').innerHTML = family[language];
    clone.querySelector('.family a').href = `/listings?item=agent&family=${family['en']}`;
    clone.querySelector('.name').innerHTML = name[language];
    clone.querySelector('.attribute-1').innerHTML = 'Agente';
    clone.querySelector('.attribute-2').innerHTML = type[language];
    clone.querySelector('.attribute-2').classList.add(classes[item.agent_type]);
    clone.querySelector('.market a').href = 'https://steamcommunity.com/market/listings/730/' + item.market_hash_name;
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

    const family =
    {
        'en': item.weapon_family,
        'br': item.weapon_family_br
    }

    if (item.weapon_stattrak)
    {
        clone.querySelector('.stattrak').innerHTML = '(StatTrak)';
    }
    
    clone.querySelector('.name').innerHTML = name[language];
    clone.querySelector('.family a').innerHTML = family[language];
    clone.querySelector('.family a').href = `/listings?item=weapon&family=${family['en']}`;
    clone.querySelector('.attribute-1').innerHTML = exterior[item.weapon_exterior][language];
    clone.querySelector('.attribute-2').innerHTML = `${type[language]} (${rarity[item.weapon_rarity][language]})`;
    clone.querySelector('.attribute-2').classList.add(classes[rarity[item.weapon_rarity]['en']]);
    clone.querySelector('.market a').href = 'https://steamcommunity.com/market/listings/730/' + item.market_hash_name;
    clone.querySelector('.image').dataset.name = `/images/${item.image}.png`;    

    const image = clone.querySelector('img');

    new_image = new Image();

    new_image.addEventListener('load', (e) =>
    {        
        image.src = e.target.src;
    });

    new_image.src = `/images/${item.image}_${item.weapon_exterior}.png`;
}

const exterior = 
{
    'fn': {'en': 'Factory New', 'br': 'Nova de Fábrica'},
    'mw': {'en': 'Minimal Wear', 'br': 'Pouca Usada'},
    'ft': {'en': 'Field-Tested', 'br': 'Testada em Campo'},
    'ww': {'en': 'Well Worm', 'br': 'Bem Desgastada'},
    'bs': {'en': 'Battle-Scarred', 'br': 'Veterana de Guerra'}
};

const AVAILABILITY =
{
    AVAILABLE: 1,
    ORDER: 2,
    COMING_SOON: 3,
    status:
    {
        1: 'Disponível',
        2: 'Sob encomenda',
        3: 'Disponível em breve'
    }
};

const rarity = {
    1: {'en': 'Consumer Grade', 'br': 'Nível Consumidor'},
    2: {'en': 'Industrial Grade', 'br': 'Nível Industrial'},
    3: {'en': 'Mil-Spec', 'br': 'Nível Militar'},
    4: {'en': 'Restricted', 'br': 'Restrito'},
    5: {'en': 'Classified', 'br': 'Secreto'},
    6: {'en': 'Covert', 'br': 'Oculto'}
};

const classes = {
    'Consumer Grade': 'consumer-grade',
    'Industrial Grade': 'industrial-grade',
    'Mil-Spec': 'mil-spec',
    'Restricted': 'restricted',
    'Classified': 'classified',
    'Covert': 'covert',
    'Distinguished': 'distinguished',
    'Exceptional': 'exceptional',
    'Superior': 'superior',
    'Master': 'master'
};