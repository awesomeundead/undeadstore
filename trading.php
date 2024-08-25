<?php

use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\TradeOffer;
use Awesomeundead\Undeadstore\TradeOfferException;

define('ROOT', __DIR__);

require ROOT . '/vendor/autoload.php';

$content = file_get_contents('https://undeadstore.com.br/status.php');
$content = json_decode($content, true);

if ($content['status'] == false)
{
    exit;
}

$pdo = Database::connect();

$query = 'SELECT * FROM trading WHERE status = :status';
$params = [
    'status' => 'pending'
];

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

foreach ($list as $item)
{
    $new_list[$item['user_id']][] = $item;
}

if (empty($new_list))
{
    exit;
}

foreach ($new_list as $user_id => $list)
{
    $query = 'SELECT steamid, steam_trade_url FROM users WHERE id = :id';
    $params = [
        'id' => $user_id
    ];

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
    $assets = [];

    foreach ($list as $item)
    {
        $assets[] = [
            'appid' => '730',
            'contextid' => '2',
            'amount' => '1',
            'assetid' => (string) $item['steam_asset']
        ];
    }

    try
    {
        $trade = TradeOffer::sendOffer($user['steam_trade_url'], $user['steamid'], $assets);

        $response = $trade['response'];
        $info = $trade['info'];
    }
    catch (TradeOfferException $e)
    {
        require ROOT . '/include/mail.php';

        $params['subject'] = 'Problemas com Trade';
        $params['message'] = $e->getMessage();
        
        send_mail($params);
    }

    file_put_contents(ROOT . '/log/trade_' . $info['http_code'] . '_user_' . $user_id . '_' . time() . '.json', $response);

    if (isset($response))
    {
        $data = json_decode($response, true);

        if (isset($data['tradeofferid']))
        {
            foreach ($list as $item)
            {
                $query = 'UPDATE trading SET tradeofferid = :tradeofferid, status = :status WHERE id = :id';
                $params = [
                    'id' => $item['id'],
                    'tradeofferid' => $data['tradeofferid'],
                    'status' => 'sent'
                ];
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
            }
        }
    }
}