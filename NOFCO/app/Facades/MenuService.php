<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class MenuService extends Facade
{
    protected static function getFacadeAccessor() { return 'menuservice'; }
}