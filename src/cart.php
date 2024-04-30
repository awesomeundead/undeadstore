<?php

$cart_items = $session->get('cart_items');

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
    $coupon = $session->get('cart_coupon');
    $message = $session->flash('coupon');

    if ($coupon)
    {
        $expiration_date = strtotime($coupon['expiration_date']);

        if (time() < $expiration_date)
        {
            $coupon_name = $coupon['name'];
            $discount = $subtotal / 100 * $coupon['percent'];
            $percent = $coupon['percent'];
        }
        else
        {
            $coupon = false;
            $session->set('cart_coupon', $coupon);
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
    $session->set('cart_subtotal', $subtotal);
    $session->set('cart_discount', $discount);
    $session->set('cart_total', $total);
}

$content_view = 'cart.phtml';
$settings_title = 'Carrinho';

require VIEW . 'layout.phtml';