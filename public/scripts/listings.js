async function main()
{
    pathname = window.location.pathname.split('/');

    if (pathname[1] == 'listings')
    {
        if (pathname.length > 2)
        {
            let weapons = ['machinegun', 'pistol', 'rifle', 'shotgun', 'smg', 'sniper-rifle'];

            if (weapons.indexOf(pathname[2]))
            {
                if (pathname.length == 3)
                {
                    url = `/list/item?type=${pathname[2]}`;
                }
                else if (pathname.length == 4)
                {
                    url = `/list/item?name=${pathname[3]}`;
                }
            }
        }
        else
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
            else if (query.has('collection'))
            {
                url = `/list/item?collection=${query.get('collection')}`;
            }
        }
    }

    data = await request(url);
    data = Object.values(data);
    create(data, fragment, container);
}

const reorder =
{
    asc: () =>
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
    },
    desc: () =>
    {
        data.sort((a, b) =>
        {
            return b.price - a.price;
        });
    },
    float: () =>
    {
        data.sort((a, b) =>
        {
            if (a.pattern_float == null || a.availability == 2)
            {
                return 1;
            }

            if (b.pattern_float == null || b.availability == 2)
            {
                return -1;
            }

            return a.pattern_float - b.pattern_float;
        });
    }
};

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
            if (e.target.attributes.href.value.startsWith('/listings'))
            {
                e.preventDefault();

                history.pushState(null, null, new URL(e.target.href));

                main().catch((error) =>
                {
                    console.log('Erro: ' + error.message);
                });
            }
        });
    }

    list = document.querySelectorAll('#nav_order a');

    for (let item of list)
    {
        item.addEventListener('click', (e) =>
        {
            e.preventDefault();
            order = e.target.dataset.order;
            
            if (reorder.hasOwnProperty(order))
            {
                reorder[order]();
                create(data, fragment, container);
            }
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
var data;