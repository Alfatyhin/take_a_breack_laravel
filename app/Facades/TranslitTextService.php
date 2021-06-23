<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class TranslitTextService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TranslitText';
    }
}
