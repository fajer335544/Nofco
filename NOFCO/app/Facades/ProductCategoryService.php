<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class ProductCategoryService extends Facade
{
    protected static function getFacadeAccessor() { return 'ProductCategoryService'; }
}