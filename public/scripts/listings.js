async function main()
{
    let query = new URLSearchParams(window.location.search);

    if (query.has('family'))
    {
        url = `/list/item?family=${query.get('family')}`;
    }
    else if (query.has('name'))
    {
        url = `/list/item?name=${query.get('name')}`;
    }    
    else if (query.has('rarity'))
    {
        url = `/list/item?rarity=${query.get('rarity')}`;
    }
    else if (query.has('type'))
    {
        url = `/list/item?type=${query.get('type')}`;
    }

    data = await request(url);
    data = Object.values(data);
    create(data, fragment, container);
}

function reorder()
{
    console.log(order);

    if (order == 'asc')
    {
        data.sort((a, b) =>
        {
            if (a.price == null || a.availability == 2)
            {
                return 1;
            }

            if (b.price == null || b.availability == 2)
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

    create(data, fragment, container);
}

window.addEventListener('popstate', async () =>
{
    main().catch((error) =>
    {
        console.log('Erro: ' + error.message);
    });
});


document.addEventListener('DOMContentLoaded', () =>
{
    list = document.querySelectorAll('nav.listings a');
    
    for (let item of list)
    {
        item.addEventListener('click', (e) =>
        {
            e.preventDefault();

            history.pushState(null, null, new URL(e.target.href));

            main().catch((error) =>
            {
                console.log('Erro: ' + error.message);
            });
        });
    }

    list = document.querySelectorAll('#nav_order a');

    for (let item of list)
    {
        item.addEventListener('click', (e) =>
        {
            e.preventDefault();
            order = e.target.dataset.order;
            reorder();
        });
    }

    main().catch((error) =>
    {
        console.log('Erro: ' + error.message);
    });
});

const fragment = document.querySelector('template');
const container = document.querySelector('#container .items');

var language = 'br';
var order;
var data;