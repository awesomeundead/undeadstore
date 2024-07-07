<?php

use Awesomeundead\Undeadstore\App;
use App\Controllers\
{
    Auth, Cart, Checkout, Coins, Home, Listings, OrderHistory, Partners, Pay, Security, Settings, Support
};

return function (App $app)
{
    $app->get('/', [Home::class, 'index']);
    $app->get('/listings', [Home::class, 'listings']);
    $app->get('/item/{id:\d+}/{name}', [Home::class, 'item']);
    
    $app->group('/auth', function ($group)
    {
        header('X-Robots-Tag: noindex, nofollow');

        $group->get('', [Auth::class, 'index']);
        $group->get('/login', [Auth::class, 'login']);
    });

    $app->get('/cs2', function ()
    {
        redirect('/');
    });

    $app->get('/cs2/', function ()
    {
        redirect('/');
    });

    $app->get('/skins', function ()
    {
        redirect('/');
    });

    $app->get('/skins/', function ()
    {
        redirect('/');
    });

    /*

    $app->get('/cart', [Cart::class, 'index']);
    $app->get('/cart/add', [Cart::class, 'add']);
    $app->get('/cart/delete', [Cart::class, 'delete']);
    $app->post('/cart/coupon', [Cart::class, 'coupon']);

    */

    $app->group('/cart', function ($group)
    {
        header('X-Robots-Tag: noindex, nofollow');

        $group->get('', [Cart::class, 'index']);
        $group->get('/add', [Cart::class, 'add']);
        $group->get('/delete', [Cart::class, 'delete']);
        $group->post('/coupon', [Cart::class, 'coupon']);
    });

    $app->get('/checkout', [Checkout::class, 'index']);
    $app->get('/checkout/end', [Checkout::class, 'end']);

    $app->get('/coins', [Coins::class, 'index']);

    $app->get('/data', [Home::class, 'data']);

    $app->post('/input/mercadopago', [Pay::class, 'input']);
    
    $app->get('/list/available', [Listings::class, 'available']);
    $app->get('/list/coming', [Listings::class, 'coming']);
    $app->get('/list/item', [Listings::class, 'item']);

    $app->get('/logout', function ()
    {
        session_start();
        session_unset();
        session_destroy();
        
        redirect();
    });

    $app->get('/order-history', [OrderHistory::class, 'index']);

    $app->get('/partners', [Partners::class, 'index']);

    $app->get('/pay', [Pay::class, 'index']);    
    $app->get('/payment/failure', [Pay::class, 'failure']);
    $app->get('/payment/pending', [Pay::class, 'pending']);
    $app->get('/payment/success', [Pay::class, 'success']);

    $app->get('/qrcode', [Pay::class, 'qrcode']);

    $app->get('/security', [Security::class, 'index']);

    $app->get('/settings', [Settings::class, 'index']);
    $app->get('/emailverification', [Settings::class, 'email_verification']);
    $app->post('/settings', [Settings::class, 'save']);

    $app->get('/support', [Support::class, 'index']);
    $app->post('/support', [Support::class, 'create']);
    $app->get('/support/ticket', [Support::class, 'ticket']);
    $app->post('/support/ticket', [Support::class, 'add']);
};