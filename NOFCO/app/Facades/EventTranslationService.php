<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class EventTranslationService extends Facade
{
    protected static function getFacadeAccessor() { return 'EventTranslationService'; }
}