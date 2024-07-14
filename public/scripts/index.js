async function main()
{
    const fragment = document.querySelector('template');

    data = await request('/list/coming');

    if (data.length > 3)
    {
        create(data, fragment, document.querySelector('#container .coming'));
    }
    else
    {
        document.querySelector('#container .coming').remove();
    }

    data = await request('/list/available');
    create(data, fragment, document.querySelector('#container .items')); 
}

main().catch((error) =>
{
    console.log('Erro: ' + error.message);
    console.log(error);
});

var language = 'br';