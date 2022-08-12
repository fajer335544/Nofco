<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class FileService extends Facade
{
    protected static function getFacadeAccessor() { return 'FileService'; }
}