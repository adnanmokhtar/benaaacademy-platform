<?php

namespace Dot\Platform\Classes;

use ReflectionClass;

/*
 * Class Plugin
 * @package Dot\Platform\Classes
 */

class Plugin
{

    /*
     * Loaded plugins
     * @var array
     */
    protected static $plugins = [];

    /*
     * @var array|\Illuminate\Config\Repository|mixed
     */
    protected $config = [];

    /*
     * Plugin constructor.
     */
    function __construct($app)
    {
        $this->app = $app;
        $this->config = $app->config["admin"]["plugins"];
    }

    /*
     * Get all plugins
     * @return array
     */
    public function all()
    {

        foreach ($this->config as $key => $class) {

            $plugin = $this->get($key, $class);

            if ($plugin) {

                $this->add($key, $plugin);

                foreach ($plugin->getRecursiveDependencies() as $key => $plugin) {
                    $this->add($key, $plugin);
                }
            }
        }

        return self::$plugins;
    }

    /**
     * Add plugin
     * @param $key
     * @param $plugin
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    function add($key, $plugin)
    {

        if (!$plugin) {
            return response("Invalid plugin $key.")->send();
        }

        self::$plugins[$key] = $plugin;
    }

    /*
     * get plugin details
     * @param $key
     * @param $class
     * @return mixed
     */
    public function get($key, $class = false)
    {

        // Check plugin is already loaded.

        if (array_key_exists($key, self::$plugins)) {
            return self::$plugins[$key];
        }

        //  Creating plugin object.

        if (class_exists($class)) {

            $reflection = new ReflectionClass($class);

            $plugin = new $class($this->app);

            $plugin->key = $key;
            $plugin->path = dirname($reflection->getFileName());
            $plugin->class = $class;
            $plugin->class_file_name = basename($reflection->getFileName());

            return $plugin;
        }
    }

    /*
     * Recursive plugins fetch
     * @param $plugin
     * @param array $plugins
     * @return array
     */
    public function getRecursive($plugin, $plugins = [])
    {

        foreach ($plugin->getDependencies() as $key => $plugin) {

            if ($plugin) {

                $plugins[$key] = $plugin;

                if (count($plugin->getDependencies())) {
                    return $this->getRecursive($plugin, $plugins);
                }
            }
        }

        return $plugins;
    }

}
