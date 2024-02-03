<?php

namespace Benaaacademy\Platform\Facades;

use Illuminate\Support\Facades\Facade;


class Benaaacademy extends Facade
{

    /*
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Benaaacademy';
    }

}
