const request = async (url, options) =>
{
    const response = await fetch(url, options);

    if (response.ok)
    {
        if (response.headers.get('Content-Type').includes('application/json'))
        {   
            return await response.json();
        }
    }

    throw new Error('NOT FOUND');
}