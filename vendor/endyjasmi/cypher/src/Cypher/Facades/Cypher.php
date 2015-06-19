<?php namespace EndyJasmi\Cypher\Facades;

use Illuminate\Support\Facades\Facade;

class Cypher extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cypher';
    }
}
