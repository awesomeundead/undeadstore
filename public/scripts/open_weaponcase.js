var array = [];
var preload = [];
var pause = false;

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

const preloadAudio = (src) => new Promise((resolve, reject) =>
{
    const audio = new Audio();
    audio.oncanplay = resolve;
    audio.onerror = reject;
    audio.src = src;
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
    for (i = 0; i < preload.length; i++)
    {
        await preloadImage(`../images/${preload[i]}.png`);
    }

    await preloadAudio('/wheel_spin.ogg');

    const sound = new Audio('/wheel_spin.ogg');

    document.querySelector('.loading').remove();
    document.querySelector('.recipient').hidden = false;

    document.querySelector('#start').addEventListener('click', async () =>
    {
        document.querySelector('.recipient').remove();
        document.querySelector('#carousel').hidden = false;

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
            if (pause && array[glide.index].img == data.image)
            {
                glide.pause();
                sound.pause();

                setTimeout(() =>
                {
                    document.querySelector('#carousel').remove();
                    document.querySelector('.prize').hidden = false;
                    
                    document.querySelector('.prize .rarity img').src = `../images/${array[glide.index].img}.png`;
                    document.querySelector('.prize .rarity').classList.add(array[glide.index].rarity);
                    document.querySelector('.prize .description').innerHTML = data.name;
                }, 1500);
            }
        });

        try
        {        
            data = await request('/inventory/weaponcase/open?id=' + item_id);

            if (data.hasOwnProperty('error'))
            {
                throw new Error('ERROR FOUND');
            }

            sound.play();
            glide.update({ animationDuration: 100 });
            glide.play(1);

            setTimeout(() =>
            {
                glide.update({ animationDuration: 200 });

                setTimeout(() =>
                {
                    pause = true;
                }, 200);
            }, 4000);
        }
        catch (error)
        {
            document.querySelector('.opencase').innerHTML = 'Estamos com problemas, tente novamente mais tarde.';
        }
    });
};

document.addEventListener('DOMContentLoaded', main);