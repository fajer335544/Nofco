<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class EventService extends Facade
{
    protected static function getFacadeAccessor() { return 'EventService'; }
}