<?php

namespace Awesomeundead\Undeadstore;

class TradeOffer
{
    private const USERAGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36';

    private static function getCookies()
    {    
        $saved_cookies = require __DIR__ . '/../trade_config.php';
    
        foreach ($saved_cookies as $name => $value)
        {
            if ($name == 'browserid' || $name == 'steamDidLoginRefresh' || $name == 'steamLoginSecure' || $name == 'timezoneOffset')
            {
                $cookies[] = "{$name}={$value}";
            }
        }
    
        if (empty($saved_cookies['steamLoginSecure']))
        {
            throw new TradeOfferException('EMPTY REQUIRED COOKIE.');
        }

        $options = [
            CURLOPT_URL => 'https://steamcommunity.com/login/',
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_USERAGENT => self::USERAGENT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_COOKIE => implode('; ', $cookies)
        ];
    
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
    
        $response = substr($response, 0, strpos($response, "\r\n\r\n"));
        $response = explode("\r\n", $response);
    
        foreach ($response as $header)
        {
            if (preg_match('/^Set-Cookie:\s*([^;]*)/i', $header, $matches))
            {
                list($name, $value) = explode('=', $matches[1]);
    
                $saved_cookies[$name] = $value;
            }
    
            $header = explode(':', $header, 2);
    
            if (count($header) >= 2)
            {
                $name = strtolower(trim($header[0]));
    
                if ($name != 'set-cookie')
                {
                    $headers[$name] = trim($header[1]);
                }
            }
        }
    
        if ($saved_cookies ?? false)
        {
            $export = var_export($saved_cookies, true);
    
            file_put_contents(__DIR__ . '/../trade_config.php', "<?php return {$export};");
        }

        $location = $headers['location'] ?? false;

        if ($location != 'https://steamcommunity.com/my/goto')
        {
            throw new TradeOfferException('INVALID COOKIES');
        }

        return [$saved_cookies['sessionid'], $saved_cookies['steamLoginSecure']];
    }

    private static function checkTradeURL($steam_trade_url)
    {
        if (!preg_match('#^https://steamcommunity.com/tradeoffer/new/\?partner=(\d+)&token=(\w+)$#', $steam_trade_url, $matches))
        {
            throw new TradeOfferException('INVALID STEAM TRADE URL.');
        }

        // steamID3 => $matches[1], token => $matches[2]
        return [$matches[1], $matches[2]];
    }

    public static function sendOffer($steam_trade_url, $steamID64, $assets)
    {
        [$sessionid, $steamLoginSecure] = self::getCookies();
        
        [$steamID3, $token] = self::checkTradeURL($steam_trade_url);

        $tradeoffer = [
            'newversion' => true,
            'version' => 4,
            'me' => [
                'assets' => $assets,
                'currency' => [],
                'ready' => false
            ],
            'them' => [
                'assets' => [
                    [
                        'appid' => '',
                        'contextid' => '',
                        'amount' => '',
                        'assetid' => ''
                    ]
                ],
                'currency' => [],
                'ready' => false
            ]
        ];

        $trade_offer_create_params = [
            'trade_offer_access_token' => $token
        ];

        $headers = [
            "Referer: https://steamcommunity.com/tradeoffer/new/?partner={$steamID3}&token={$token}"
        ];

        $body = [
            'sessionid' => $sessionid,
            'serverid' => '1',
            'partner' => $steamID64,
            'tradeoffermessage' => 'Agradecemos a sua compra na Undead Store',
            'json_tradeoffer' => json_encode($tradeoffer),
            'captcha' => '',
            'trade_offer_create_params' => json_encode($trade_offer_create_params),
            'tradeofferid_countered' => ''
        ];

        $options = [
            CURLOPT_URL => 'https://steamcommunity.com/tradeoffer/new/send',
            CURLOPT_USERAGENT => self::USERAGENT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_COOKIE => "sessionid={$sessionid}; steamLoginSecure={$steamLoginSecure}"
        ];

        $curl = curl_init();

        curl_setopt_array($curl, $options);

        return ['response' => curl_exec($curl), 'info' => curl_getinfo($curl)];
    }
}

class TradeOfferException extends \Exception {}