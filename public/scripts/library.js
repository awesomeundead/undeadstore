create = (data, fragment, container) =>
{
    container.innerHTML = '';

    for (let key in data)
    {
        template(data[key], fragment, container);
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

    clone.querySelector('.title a').href = `/item/${item.id}/${item.market_hash_name}`;
    clone.querySelector('.image a').href = `/item/${item.id}/${item.market_hash_name}`;

    const type = {'en': item.type, 'br': item.type_br}

    const name = {'en': item.name, 'br': item.name_br}

    const family = {'en': item.family, 'br': item.family_br}

    if (item.category && item.category != 'normal')
    {
        clone.querySelector('.category').innerHTML = categories[item.category][language];
        clone.querySelector('.category').classList.add(item.category);
    }
    
    clone.querySelector('.name').innerHTML = name[language];
    clone.querySelector('.family').innerHTML = family[language];

    if (item.exterior)
    {
        clone.querySelector('.attribute-1').innerHTML = exterior[item.exterior][language];
    }
    else
    {
        clone.querySelector('.attribute-1').remove();
    }
    
    clone.querySelector('.attribute-2').innerHTML = `${type[language]} (${rarity[item.rarity][language]})`;
    clone.querySelector('.attribute-2').classList.add(item.rarity);
    clone.querySelector('.market a').href = 'https://steamcommunity.com/market/listings/730/' + item.market_hash_name;   

    const image = clone.querySelector('img');

    new_image = new Image();

    new_image.addEventListener('load', (e) =>
    {        
        image.src = e.target.src;
    });

    if (item.type == 'Agent')
    {
        new_image.src = `/images/${item.image}.png`;
    }
    else
    {
        image_exterior = {'fn': 'fn_mw', 'mw': 'fn_mw', 'ft': 'ft_ww', 'ww': 'ft_ww', 'bs': 'bs'};

        new_image.src = `/images/${item.image}_${image_exterior[item.exterior]}.png`;
    }
    
    container.appendChild(clone);
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

const categories =
{
    'normal': {'en': 'Normal', 'br': 'Normal'},
    'tournament': {'en': 'Souvenir', 'br': 'Lembrança'},
    'strange': {'en': 'StatTrak™', 'br': 'StatTrak™'},
    'unusual': {'en': '★', 'br': '★'},
    'unusual_strange': {'en': '★ StatTrak™', 'br': '★ StatTrak™'}
};

const exterior = 
{
    'fn': {'en': 'Factory New', 'br': 'Nova de Fábrica'},
    'mw': {'en': 'Minimal Wear', 'br': 'Pouca Usada'},
    'ft': {'en': 'Field-Tested', 'br': 'Testada em Campo'},
    'ww': {'en': 'Well Worm', 'br': 'Bem Desgastada'},
    'bs': {'en': 'Battle-Scarred', 'br': 'Veterana de Guerra'}
}

const rarity = {
    'common': {'en': 'Base Grade', 'br': ''},
    'rare': {'en': 'High Grade', 'br': ''},
    'mythical': {'en': 'Remarkable', 'br': ''},
    'legendary': {'en': 'Exotic', 'br': ''},
    'ancient': {'en': 'Extraordinary', 'br': ''},
    'contraband': {'en': 'Contraband', 'br': 'Contrabando'},
    'common_weapon': {'en': 'Consumer Grade', 'br': 'Nível Consumidor'},
    'uncommon_weapon': {'en': 'Industrial Grade', 'br': 'Nível Industrial'},
    'rare_weapon': {'en': 'Mil-Spec', 'br': 'Nível Militar'},
    'mythical_weapon': {'en': 'Restricted', 'br': 'Restrito'},
    'legendary_weapon': {'en': 'Classified', 'br': 'Secreto'},
    'ancient_weapon': {'en': 'Covert', 'br': 'Oculto'},
    'rare_character': {'en': 'Distinguished', 'br': 'Distinto'},
    'mythical_character': {'en': 'Exceptional', 'br': 'Excepcional'},
    'legendary_character': {'en': 'Superior', 'br': 'Superior'},
    'ancient_character': {'en': 'Master', 'br': 'Mestre'}
};