<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class CurrencyService extends Facade
{
    protected static function getFacadeAccessor() { return 'CurrencyService'; }
}