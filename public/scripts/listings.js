async function main()
{
    data = await request('/listings/family?item=' + item_type);
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
    item_type = new URLSearchParams(window.location.search).get('item');

    main().catch((error) =>
    {
        console.log('Erro: ' + error.message);
    });
});


document.addEventListener('DOMContentLoaded', () =>
{
    const nav_weapon_list = document.querySelectorAll('#nav_weapon a');
    
    for (let item of nav_weapon_list)
    {
        item.addEventListener('click', (e) =>
        {
            e.preventDefault();
            query = new URL(e.target.href).search;
            item_type = new URLSearchParams(query).get('item');   

            main().catch((error) =>
            {
                console.log('Erro: ' + error.message);
            });

            history.pushState(null, null, '?item=' + item_type);
        });
    }

    const nav_list = document.querySelectorAll('#nav_order a');

    for (let item of nav_list)
    {
        item.addEventListener('click', (e) =>
        {
            e.preventDefault();
            order = e.target.dataset.order;
            reorder();
        });
    }

    if (QUERY_STRING.has('item'))
    {
        item_type = QUERY_STRING.get('item');
    }

    main().catch((error) =>
    {
        console.log('Erro: ' + error.message);
    });
});

const fragment = document.querySelector('template');
const container = document.querySelector('#container');

var language = 'br';
var item_type;
var order;
var data;