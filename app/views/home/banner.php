<div class="banner">
    <a href="/listings?name=AK-47">
        <picture>
            <source media="(min-width: 769px)" srcset="/detach/banner_ak_47.webp" type="image/webp" />
            <source media="(min-width: 769px)" srcset="/detach/banner_ak_47.png" type="image/png" />
            <img alt="AK-47" src="/detach/banner_ak_47.png" />
        </picture>
    </a>
    <a hidden="true" href="/listings?name=AWP">
        <picture>
            <source media="(min-width: 769px)" srcset="/detach/banner_awp.webp" type="image/webp" />
            <source media="(min-width: 769px)" srcset="/detach/banner_awp.png" type="image/png" />
            <img alt="AWP" src="/detach/banner_awp.png" />
        </picture>
    </a>
    <a hidden="true" href="/listings?name=M4A4">
        <picture>
            <source media="(min-width: 769px)" srcset="/detach/banner_m4a4.webp" type="image/webp" />
            <source media="(min-width: 769px)" srcset="/detach/banner_m4a4.png" type="image/png" />
            <img alt="M4A4" src="/detach/banner_m4a4.png" />
        </picture>
    </a>
</div>
<script>

list = document.querySelectorAll('.banner a');
count = list.length;
index = 1;
last = list[0];

setInterval(() =>
{
    if (list[index].hidden)
    {
        image = list[index].querySelector('img');

        if (image.complete)
        {
            last.hidden = true;
            list[index].hidden = false;
            last = list[index];
        }
    }

    index++;

    if (index == count)
    {
        index = 0;
    }   
}, 1000 * 5);

</script>