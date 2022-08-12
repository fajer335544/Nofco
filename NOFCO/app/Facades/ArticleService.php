<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class ArticleService extends Facade
{
    protected static function getFacadeAccessor() { return 'ArticleService'; }
}