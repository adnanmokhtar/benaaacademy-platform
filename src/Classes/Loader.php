<?php

namespace Benaaacademy\Platform\Classes;

class Loader
{

    /*
     * @var array
     */
    protected static $directories = [];

    /*
     * @param $directories
     * Fetching directories
     */
    public static function add($directories)
    {
        if (!is_array($directories)) {
            $directories = [$directories];
        }

        static::$directories = array_merge(static::$directories, $directories);
    }


    /*
     * Fetching directories files
     */
    public static function register()
    {
        foreach (static::$directories as $directory) {
            foreach (glob($directory . '/*.php') as $file) {
                require_once($file);
            }
        }
    }

}
