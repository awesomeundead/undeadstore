<?php

use Awesomeundead\Undeadstore\App;
use Awesomeundead\Undeadstore\Database;
use Awesomeundead\Undeadstore\Session;
use App\Controllers\
{
    Auth, Cart, Checkout, Home, Orders, Pay, Settings, Support
};

return function (App $app)
{
    $app->get('/', [Home::class, 'index']);
    
    $app->get('/auth', [Auth::class, 'index']);
    $app->get('/auth/login', [Auth::class, 'login']);

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

    $app->get('/cart', [Cart::class, 'index']);
    $app->get('/cart/add', [Cart::class, 'add']);
    $app->get('/cart/delete', [Cart::class, 'delete']);
    $app->post('/cart/coupon', [Cart::class, 'coupon']);

    $app->get('/checkout', [Checkout::class, 'index']);
    $app->get('/checkout/end', [Checkout::class, 'end']);
    $app->post('/checkout/trade', [Checkout::class, 'trade']);

    $app->get('/data', [Home::class, 'data']);

    $app->post('/input/mercadopago', [Pay::class, 'input']);

    $app->get('/logout', function ()
    {
        session_start();
        session_unset();
        session_destroy();
        
        redirect();
    });

    $app->get('/orders', [Orders::class, 'index']);

    $app->get('/partners', function ()
    {
        $session = Session::create();

        $content_view = 'partners.phtml';
        $settings_title = 'Parceiros';

        require VIEW . 'layout.phtml';
    });

    $app->get('/pay', [Pay::class, 'index']);    
    $app->get('/payment/failure', [Pay::class, 'failure']);
    $app->get('/payment/pending', [Pay::class, 'pending']);
    $app->get('/payment/success', [Pay::class, 'success']);

    $app->get('/qrcode', [Pay::class, 'qrcode']);

    $app->get('/settings', [Settings::class, 'index']);

    $app->post('/settings', [Settings::class, 'save']);

    $app->get('/support', [Support::class, 'index']);

    $app->post('/support', [Support::class, 'create']);

    $app->get('/support/ticket', [Support::class, 'ticket']);

    $app->post('/support/ticket', [Support::class, 'add']);
};