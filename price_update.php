<?php

use Awesomeundead\Undeadstore\Database;

define('ROOT', __DIR__);

require ROOT . '/vendor/autoload.php';

function request($market_hash_name)
{
    $url = 'https://steamcommunity.com/market/listings/730/' . rawurlencode($market_hash_name) . '/render?start=0&count=6&currency=7&format=json';

    $context = stream_context_create([
        'http' => ['ignore_errors' => true]
    ]);
    
    $response['content'] = file_get_contents($url, false, $context);

    foreach ($http_response_header as $item)
    {
        //HTTP/1.1 200 OK
        if (preg_match('#HTTP/\d\.\d\s(\d+)\s([\w\s]+)#', $item, $matches))
        {
            $response['status'] = $matches[1];
            $response['status_text'] = $matches[2];
        }
    }

    if ($response['content'])
    {
        $response['content'] = json_decode($response['content'], true);
    }

    return $response;
}

$pdo = Database::connect();

$query = 'SELECT products.*, cs_item_variant.market_hash_name FROM products
LEFT JOIN cs_item_variant ON products.cs_item_variant_id = cs_item_variant.id
WHERE availability = 1';
$stmt = $pdo->prepare($query);
$stmt->execute();
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($list as $item)
{
    if ($item['updated_date'] == date('Y-m-d'))
    {
        continue;
    }

    $response = request($item['market_hash_name']);

    if ($response['status'] != 200)
    {
        break;
    }

    $content = $response['content'];

    if (isset($content['listinginfo']))
    {
        $prices = [];

        foreach ($content['listinginfo'] as $asset)
        {
            if (isset($asset['converted_price']) && isset($asset['converted_fee']))
            {
                $value = $asset['converted_price'] + $asset['converted_fee'];
                $value = round($value * 0.01, 2);
                $prices[] = $value;
            }
        }

        if (count($prices) < 6)
        {
            continue;
        }

        $average = round(array_sum($prices) / 6, 2);

        $percentage = (float) $item['base_price_percentage'] ?? 10;
        $new_price = round($average - ($average / 100 * $percentage));

        $query = 'UPDATE products SET price = :price, updated_date = :updated_date WHERE id = :id';
        $params = [
            'id' => $item['id'],
            'price' => $new_price,
            'updated_date' => date('Y-m-d')
        ];

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
    }
}