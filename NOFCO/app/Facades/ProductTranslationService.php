<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class ProductTranslationService extends Facade
{
    protected static function getFacadeAccessor() { return 'ProductTranslationService'; }
}