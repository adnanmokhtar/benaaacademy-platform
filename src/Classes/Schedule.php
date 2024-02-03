<?php

namespace Benaaacademy\Platform\Classes;

use Illuminate\Console\Scheduling\Schedule as Schedular;

/*
 * Class BenaaacademySchedule
 * @package Benaaacademy\Platform
 */
class Schedule
{

    /*
     * @param bool $callback
     */
    public static function run($callback = false)
    {

        app()->booted(function () use ($callback) {

            $schedule = app()->make(Schedular::class);

            if (is_callable($callback)) {
                return $callback($schedule);
            }

        });

    }

}
