<?php

namespace Benaaacademy\Platform\Classes;

/*
 * Class Benaaacademy
 * Benaaacademy super class
 * @package Benaaacademy\Platform
 */
class Benaaacademy
{

    /*
     *  Benaaacademy platform version
     */
    const VERSION = '0.3.4';

    /*
     * The current globally available container.
     *
     * @var static
     */
    protected static $instance;

    /*
     * Default system bindings
     * @var array
     */
    public $defaultBindings = [

    ];

    /*
     * Default instances
     * @var array
     */
    public $instances = [];

    /*
     * BenaaacademyPlatform constructor.
     */
    function __construct()
    {
        $this->app = app();
    }

    /*
     * Class Factory
     */
    function loadBenaaacademyBindings()
    {
        foreach ($this->instances as $name => $instance) {
            $this->app->bind($name, function () use ($instance) {
                return $instance;
            });
        }
    }

    /*
     * @param $name
     * @param $callback
     */
    function extend($name, $callback)
    {
        self::getInstance()->instances[$name] = $callback($this);
    }

    /*
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /*
     * get all system locales
     * @return array
     */
    public function getLocales()
    {
        $locales = [];

        foreach ((array)$this['config']->get("admin.locales") as $code => $locale) {
            $locales[$code] = [
                "title" => isset($locale["title"]) ? $locale["title"] : $code,
                "direction" => isset($locale["direction"]) ? $locale["direction"] : "ltr"
            ];
        }

        return $locales;
    }

    /*
     * @return string
     */
    public function version()
    {
        return $this::VERSION;
    }

    /*
     * @param $path
     * @return string
     */
    function getPluginClass($path)
    {
        return "Plugin";
    }

    /*
     *
     */
    function forbidden()
    {
        response(view("admin::errors.forbidden")->render(), 403)->send();
        exit();
    }

    /*
     * @param $name
     * @param $arguments
     * @return null
     */
    public function __call($name, $arguments)
    {
        return self::getInstance()->get($name);
    }

    /*
     * @param $name
     * @return null
     */
    function get($name)
    {
        return $this->has($name) ? self::getInstance()->instances[$name] : NULL;
    }

    /*
     * @param $name
     * @return null
     */
    function has($name)
    {
        return array_key_exists($name, $this->instances);
    }


}
