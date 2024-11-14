<?php

use Awesomeundead\Undeadstore\App;
use App\Controllers\
{
    Auth, Cart, Checkout, Home, Inventory, Listings, OrderHistory, Partners, Payment, Reference, Security, Settings, Support
};

return function (App $app)
{
    $app->get('/', [Home::class, 'index']);
    $app->get('/listings', [Home::class, 'listings']);
    $app->get('/item/{id:\d+}/{name}', [Home::class, 'item']);

    $app->get('/listings/{type:[\w-]+}[/{name:[\w-]+}]', [Home::class, 'listings']);
    
    $app->group('/auth', function ($group)
    {
        $group->get('', [Auth::class, 'index']);
        $group->get('/login', [Auth::class, 'login']);
    });

    $app->group('/cart', function ($group)
    {
        $group->get('', [Cart::class, 'index']);
        $group->get('/add', [Cart::class, 'add']);
        $group->get('/delete', [Cart::class, 'delete']);
        $group->post('/coupon', [Cart::class, 'coupon']);
    });

    $app->get('/checkout', [Checkout::class, 'index']);
    $app->get('/checkout/end', [Checkout::class, 'end']);

    $app->get('/data', [Home::class, 'data']);

    $app->get('/inventory', [Inventory::class, 'index']);
    $app->post('/inventory/calc', [Inventory::class, 'calc']);
   
    $app->get('/list/available', [Listings::class, 'available']);
    $app->get('/list/under', [Listings::class, 'under']);
    $app->get('/list/coming', [Listings::class, 'coming']);
    $app->get('/list/item', [Listings::class, 'item']);

    $app->get('/logout', function ()
    {
        session_start();

        if (isset($_COOKIE['login']))
        {
            unset($_COOKIE['login']);
            setcookie('login', '', -1);
        }

        session_unset();
        session_destroy();
        redirect();
    });

    $app->get('/order-history', [OrderHistory::class, 'index']);

    $app->get('/partners', [Partners::class, 'index']);

    $app->get('/payment', [Payment::class, 'index']);
    $app->post('/payment/process', [Payment::class, 'process']);
    $app->post('/payment/notification', [Payment::class, 'notification']);
    $app->get('/payment/update', [Payment::class, 'update']);
    $app->get('/payment/wallet', [Payment::class, 'wallet']);

    $app->get('/security', [Security::class, 'index']);

    $app->get('/settings', [Settings::class, 'index']);
    $app->get('/emailverification', [Settings::class, 'email_verification']);
    $app->post('/settings', [Settings::class, 'save']);

    $app->get('/support', [Support::class, 'index']);
    $app->post('/support', [Support::class, 'create']);
    $app->get('/support/ticket', [Support::class, 'ticket']);
    $app->post('/support/ticket', [Support::class, 'add']);

    $app->get('/reference', [Reference::class, 'index']);
    $app->get('/reference/{type:[\w-]+}[/{name:[\w-]+}]', [Reference::class, 'listing']);
};