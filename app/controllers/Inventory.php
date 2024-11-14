<?php

namespace App\Controllers;

use Awesomeundead\Undeadstore\Controller;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;

class Inventory extends Controller
{
    private function request($url)
    {
        $context = stream_context_create([
            'http' => ['ignore_errors' => true]
        ]);

        $response['body'] = file_get_contents($url, false, $context);

        foreach ($http_response_header as $item)
        {
            //HTTP/1.1 200 OK
            if (preg_match('#HTTP/\d\.\d\s(\d+)\s([\w\s]+)#', $item, $matches))
            {
                $response['status'] = $matches[1];
                $response['status_text'] = $matches[2];
            }
        }

        if ($response['body'])
        {
            $response['body'] = json_decode($response['body'], true);
        }

        return $response;
    }

    public function index()
    {
        $session = Session::create();

        $logged_in = $session->get('logged_in');

        if (!$logged_in)
        {
            redirect('/auth?redirect=inventory');
        }

        $steamid = $session->get('steamid');

        $response = $this->request("https://steamcommunity.com/inventory/{$steamid}/730/2/?l=brazilian");

        $list = [];

        if ($response['status'] == 200)
        {
            $content = $response['body'];

            foreach ($content['descriptions'] as $item)
            {
                $descriptions[$item['classid']] = $item;
            }
    
            foreach ($content['assets'] as $item)
            {
                $description = $descriptions[$item['classid']];

                if ($description['marketable'])
                {
                    preg_match('/([\w\s]+)\s\((.+)\)/u', $description['type'], $matches);

                    if (isset($_GET['type']) && $_GET['type'] != $matches[1])
                    {
                        continue;
                    }

                    $list[] = [
                        'assetid' => $item['assetid'],
                        'name' => $description['name'],
                        'name_color' => $description['name_color'],
                        'market_name' => $description['market_name'],
                        'market_hash_name' => $description['market_hash_name'],
                        'image' => "https://community.fastly.steamstatic.com/economy/image/{$description['icon_url']}/96fx96f"
                    ];
                }
            }
        }
        else
        {
            $error = 'Verifique se a privacidade do seu inventário está definida como pública.';
        }

        echo $this->templates->render('inventory/index', [
            'list' => $list,
            'error' => $error ?? null
        ]);
    }

    public function calc()
    {
        $session = Session::create();

        $logged_in = $session->get('logged_in');

        if (!$logged_in)
        {
            redirect('/auth?redirect=inventory');
        }

        $list = $_POST['item'];

        if (empty($list))
        {
            redirect('/inventory');
        }

        foreach ($list as $key => $item)
        {
            [$market_hash_name, $name, $image] = explode(';', $item);
            
            //$response = $this->request('https://steamcommunity.com/market/listings/730/' . rawurlencode($market_hash_name) . '/render?start=0&count=6&currency=7&format=json');
            $response = $this->request('https://steamcommunity.com/market/priceoverview/?appid=730&market_hash_name=' . rawurlencode($market_hash_name) . '&currency=7');

            if ($response['status'] != 200)
            {
                continue;
            }            

            $lowest_price = $response['body']['lowest_price'] ?? null;

            if (isset($lowest_price))
            {
                $lowest_price = str_replace(['R$', '.'], '', $lowest_price);
                $lowest_price = str_replace(',', '.', $lowest_price);
                $lowest_price = floatval($lowest_price);
                $new_price = round($lowest_price - ($lowest_price / 100 * 15), 1, PHP_ROUND_HALF_DOWN);

                $rows[$key]['market_hash_name'] = $market_hash_name;
                $rows[$key]['name'] = $name;
                $rows[$key]['image'] = $image;
                $rows[$key]['amount'] = $new_price;

                $amount[] = $new_price;
            }

            /*
            if (isset($response['body']['listinginfo']))
            {
                $array = $response['body']['assets']['730']['2'] ?? null;
                $first = array_shift($array);

                if ($first['commodity'] == 1)
                {
                    //$amount[] = $rows[$key]['amount'] = $new_price;
                }
                else
                {
                    $prices = [];

                    foreach ($response['body']['listinginfo'] as $asset)
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
                    $new_price = round($average - ($average / 100 * 15));

                    $amount[] = $rows[$key]['amount'] = $new_price;
                }
            }
            */
        }

        if (empty($rows))
        {
            redirect('/inventory');
        }

        $total = array_sum($amount);
        
        echo $this->templates->render('inventory/calc', [
            'rows' => $rows,
            'total' => $total
        ]);
    }
}