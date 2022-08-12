<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class ProductService extends Facade
{
    protected static function getFacadeAccessor() { return 'ProductService'; }
}