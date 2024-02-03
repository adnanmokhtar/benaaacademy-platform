<?php

namespace Benaaacademy\Platform;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/*
 * Class ServiceProvider
 * @package Benaaacademy\Platform
 */
class ServiceProvider extends LaravelServiceProvider
{

    /*
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /*
     * Default bindings
     * @var array
     */
    protected $helpers = [
        "Benaaacademy" => \Benaaacademy\Platform\Classes\Benaaacademy::class,
        "plugin" => \Benaaacademy\Platform\Classes\Plugin::class,
        "widget" => \Benaaacademy\Platform\Classes\Widget::class,
        "action" => \Benaaacademy\Platform\Classes\Action::class,
        "navigation" => \Benaaacademy\Platform\Classes\Navigation::class,
        "schedule" => \Benaaacademy\Platform\Classes\Schedule::class
    ];

    /*
     * Platform plugins
     * @var array
     */
    protected $plugins = [];


    function __construct(Application $app)
    {
        parent::__construct($app);
        foreach ($this->helpers as $abstract => $class) {
            $app->bind($abstract, function () use ($class, $app) {
                return new $class($app);
            });
        }
    }

    /*
     * Booting plugins
     */
    function boot()
    {
        foreach ($this->plugins as $plugin) {
            $plugin->boot($this);
        }
    }

    /*
     * Registering plugins
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(__DIR__ . "/../config/admin.php", "admin");

        $this->plugins = $this->app->plugin->all();

        foreach ($this->plugins as $plugin) {
            $plugin->register($this);
        }
    }
}

