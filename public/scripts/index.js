async function main()
{
    const fragment = document.querySelector('template');
    const carousel = document.querySelector('#carousel .container');

    data = await request('/list/coming');
    create(data, fragment, carousel);

    data = await request('/list/available');
    create(data, fragment, document.querySelector('#container'));    

    document.querySelector('.side.left button').addEventListener('click', () =>
    {
        let width = carousel.offsetWidth;

        if (carousel.scrollLeft == 0)
        {
            carousel.scrollLeft = carousel.scrollWidth - width;
        }
        else
        {
            carousel.scrollLeft -= width + 10;
        }
    });

    document.querySelector('.side.right button').addEventListener('click', () =>
    {
        let width = carousel.offsetWidth;

        if (carousel.scrollLeft == (carousel.scrollWidth - width))
        {
            carousel.scrollLeft = 0;
        }
        else
        {
            carousel.scrollLeft += width + 10;
        }
    });
}

main().catch((error) =>
{
    console.log('Erro: ' + error.message);
});

var language = 'br';