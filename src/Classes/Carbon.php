<?php

namespace Benaaacademy\Platform\Classes;

/*
 * Class Carbon
 * @package Benaaacademy\Platform\Classes
 */
class Carbon extends \Carbon\Carbon
{

    /*
     * Generate date string
     * @return false|\Response|string
     */
    function render()
    {
        $date_format = option("site_date_format", "relative");

        if ($date_format == "relative") {

            if (option("site_locale", app()->getLocale()) == "ar") {
                return $this->ago();
            } else {
                return $this->diffForHumans();
            }

        } else {
            return date($date_format, strtotime($this->toDateTimeString()));
        }
    }
}
