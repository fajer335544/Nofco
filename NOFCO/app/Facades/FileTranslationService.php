<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class FileTranslationService extends Facade
{
    protected static function getFacadeAccessor() { return 'FileTranslationService'; }
}