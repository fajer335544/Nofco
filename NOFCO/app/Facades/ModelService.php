<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class ModelService extends Facade
{
    protected static function getFacadeAccessor() { return 'ModelService'; }
}