<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class ArticleCategoryService extends Facade
{
    protected static function getFacadeAccessor() { return 'ArticleCategoryService'; }
}