<?php

function html_date(string $string)
{
    return date_format(date_create($string), 'd/m/Y');
}

function html_datetime(string $string)
{
    return date_format(date_create($string), 'd/m/Y H:i');
}

function html_escape(string $string)
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5);
}

function html_money(string $string)
{
    return 'R$ ' . number_format($string, 2, ',', '.');
}

function create_external_reference($purchase_id)
{
    return 'US' . str_pad($purchase_id, 5, 0, STR_PAD_LEFT);
}