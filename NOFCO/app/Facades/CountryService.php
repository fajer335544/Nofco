<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class CountryService extends Facade
{
    protected static function getFacadeAccessor() { return 'CountryService'; }
}