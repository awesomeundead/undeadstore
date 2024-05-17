async function main()
{
    const fragment = document.querySelector('template');
    const carousel = document.querySelector('#carousel .container');

    data = await request('/listings/coming');
    create(data, fragment, carousel);

    data = await request('/listings/available');
    create(data, fragment, document.querySelector('#container'));    

    document.querySelector('.side.left button').addEventListener('click', (e) =>
    {
        if (carousel.scrollLeft == 0)
        {
            carousel.scrollLeft = carousel.scrollWidth - 1200;
        }
        else
        {
            carousel.scrollLeft -= 1210
        }
    });

    document.querySelector('.side.right button').addEventListener('click', (e) =>
    {
        if (carousel.scrollLeft == (carousel.scrollWidth - 1200))
        {
            carousel.scrollLeft = 0;
        }
        else
        {
            carousel.scrollLeft += 1210
        }
    });
}

main().catch((error) =>
{
    console.log('Erro: ' + error.message);
});

var language = 'br';