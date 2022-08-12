<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class ColorService extends Facade
{
    protected static function getFacadeAccessor() { return 'ColorService'; }
}