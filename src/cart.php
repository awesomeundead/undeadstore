<?php

$logged_in = $_SESSION['logged_in'] ?? false;

if ($logged_in)
{
    $steamid = $_SESSION['user']['steamid'];
    $steam_name = $_SESSION['user']['personaname'];
    $steam_avatar = $_SESSION['user']['avatar'];
}

$cart_items = $_SESSION['cart']['items'] ?? false;

if ($cart_items)
{
    $count = count($cart_items);

    foreach ($cart_items as $index => $item)
    {
        $prices[] = $item['offer_price'] ?? $item['price'];
    }
    
    $subtotal = array_sum($prices);
    $discount = 0;
    $percent = 0;

    $coupon = $_SESSION['cart']['coupon'] ?? false;

    if ($coupon)
    {
        $expiration_date = strtotime($coupon['expiration_date']);
        $timestamp = time();

        if ($timestamp < $expiration_date)
        {
            $coupon_name = $coupon['name'];
            $discount = $subtotal / 100 * $coupon['percent'];
            $percent = $coupon['percent'];
        }
        else
        {
            $_SESSION['cart']['coupon'] = $coupon = false;
            $message = 'Cupom expirado.';
        }
    }

    if (!$coupon)
    {
        if ($count > 2)
        {
            $discount = $subtotal / 100 * 10;
            $percent = 10;
        }
        elseif ($count > 1)
        {
            $discount = $subtotal / 100 * 5;
            $percent = 5;
        }
    }

    $total = $subtotal - $discount;

    $_SESSION['cart']['subtotal'] = $subtotal;
    $_SESSION['cart']['discount'] = $discount;
    $_SESSION['cart']['total'] = $total;
}

if (isset($_SESSION['__flash']))
{
    $message = $_SESSION['__flash'];
    unset($_SESSION['__flash']);
}

$content_view = 'cart.phtml';
$settings_title = 'Carrinho';

require VIEW . 'layout.phtml';