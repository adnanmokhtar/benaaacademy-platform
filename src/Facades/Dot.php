<?php

namespace Dot\Platform\Facades;

use Illuminate\Support\Facades\Facade;


class Dot extends Facade
{

    /*
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dot';
    }

}
