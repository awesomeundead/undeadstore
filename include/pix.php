<?php

function format($id, $value)
{
    return $id . str_pad(strlen($value), 2, '0', STR_PAD_LEFT) . $value;
}

function crc16($data)
{
    $result = 0xFFFF;

    for ($i = 0; $i < strlen($data); $i++)
    {
        $result ^= (ord($data[$i]) << 8);

        for ($j = 0; $j < 8; $j++)
        {
            if ($result & 0x8000)
            {
                $result = ($result << 1) ^ 0x1021;
            }
            else
            {
                $result <<= 1;
            }

            $result &= 0xFFFF;
        }
    }

    return strtoupper(str_pad(dechex($result), 4, '0', STR_PAD_LEFT));
}

function pix($params)
{
    $code[] = '000201';
    $description = empty($params['description']) ? '' : format('02', $params['description']);
    $code[]= format('26', '0014BR.GOV.BCB.PIX' . format('01', $params['key']) . $description);
    $code[]= '52040000';
    $code[] = '5303986';

    if ($params['value'] > 0)
    {
        $code[] = format('54', number_format($params['value'], 2, '.', ''));
    }

    $code[] = '5802BR';
    $code[] = format('59', $params['name']);
    $code[] = format('60', $params['city']);
    $code[] = format('62', format('05', $params['identifier'] ?: '***'));
    $code[] = '6304';
    $string = implode('', $code);
    $crc16 = crc16($string);
    
    return $string . $crc16;
}