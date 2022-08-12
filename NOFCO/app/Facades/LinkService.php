<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class LinkService extends Facade
{
    protected static function getFacadeAccessor() { return 'linkservice'; }
}