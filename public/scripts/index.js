async function main()
{
    const fragment = document.querySelector('template');
    const carousel = document.querySelector('#carousel .container');

    data = await request('/list/coming');

    if (data.lenght)
    {
        create(data, fragment, carousel);

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
    else
    {
        document.querySelector('#carousel').remove();
    }

    data = await request('/list/available');
    create(data, fragment, document.querySelector('#container')); 
}

main().catch((error) =>
{
    console.log('Erro: ' + error.message);
    console.log(error);
});

var language = 'br';