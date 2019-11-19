<?php
namespace CodingFoundry\AutotaskAPI;

use Illuminate\Support\Facades\Facade;

class AutotaskAPIFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AutotaskAPI";
    }
}
