async function main()
{
    const fragment = document.querySelector('template');
    const query = new URLSearchParams(window.location.search);

    if (query.has('under'))
    {
        url = `/list/under?price=${query.get('under')}`;
    }
    else
    {
        url = '/list/available';
    }

    const data = await request(url);
    
    create(data, fragment, document.querySelector('#container .items')); 
}

main().catch((error) =>
{
    console.log('Erro: ' + error.message);
    console.log(error);
});

var language = 'br';