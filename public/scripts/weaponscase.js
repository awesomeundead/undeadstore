var array = [];
var preload = [];

Object.entries(rarities).forEach(entry =>
{
    const [key, value] = entry;

    items_rarity[key].forEach(element =>
    {
        preload.push(element);

        for (let i = 0; i < value; i++)
        {
            array.push({img: element, rarity: key});
        }
    });
});

array.sort(() => Math.random() - 0.5); // embaralha o array

const preloadImage = (src) => new Promise((resolve, reject) =>
{
    const image = new Image();
    image.onload = resolve;
    image.onerror = reject;
    image.src = src;
});

const request = async (url) =>
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

const main = async () =>
{
    document.querySelector('#start').addEventListener('click', async () =>
    {
        var pause = true;

        document.querySelector('.sem_nome').hidden = true;
        document.querySelector('#carousel').hidden = false;

        for (i = 0; i < preload.length; i++)
        {
            await preloadImage(`../images/${preload[i]}.png`);
        }

        //await preloadAudio('wheel_spin.mp3');

        //const sound = new Audio('wheel_spin.mp3');

        document.querySelector('.loading').remove();

        ul = document.querySelector('ul.glide__slides');
        li = document.querySelector('li.glide__slide');

        array.forEach(element =>
        {
            clone = li.cloneNode(true);
            clone.querySelector('div').className = `rarity ${element.rarity}`;
            clone.querySelector('img').src = `../images/${element.img}.png`;
            ul.appendChild(clone);
        });

        li.remove();

        const glide = new Glide('.glide', {
            animationTimingFunc: 'linear',
            focusAt: 'center',
            hoverpause: false,
            perView: 5,
            touchAngle: 0,
            type: 'carousel'
        });    

        glide.mount();

        glide.on('move', () =>
        {
            if (pause && array[glide.index].img == data.item)
            {
                glide.pause();
            }
        });

        //document.querySelector('#carousel .container').hidden = false;
        
        data = await request('/inventory/opencase?id=' + item_id);

        glide.update({ animationDuration: 100 });
        glide.play(1);

        setTimeout(() =>
        {
            glide.update({ animationDuration: 200 });

            setTimeout(() =>
            {
                pause = true;
            }, 1000);
        }, 4000);

        pause = false;

        document.querySelector('#start').hidden = true;
    });
};

document.addEventListener('DOMContentLoaded', main);