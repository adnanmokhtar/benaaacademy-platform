<?php

namespace Benaaacademy\Platform;

use Benaaacademy\Platform\Facades\Plugin as PluginFacade;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/*
 * Plugin Super class
 * @package Benaaacademy\Platform
 */

class Plugin extends ServiceProvider
{

    /*
     * Plugin dependencies
     * @var array
     */
    protected $dependencies = [];

    /*
     * Plugin service providers
     * @var array
     */
    protected $providers = [];

    /*
     * Plugin aliases
     * @var array
     */
    protected $aliases = [];

    /*
     * Plugin commands
     * @var array
     */
    protected $commands = [];

    /*
     * Plugin middlewares
     * @var array
     */
    protected $middlewares = [];

    /*
     * Plugin route middlewares
     * @var array
     */
    protected $route_middlewares = [];

    /*
     * Plugin permissions
     * @var array
     */
    protected $permissions = [];

    /*
     * Plugin composer files
     * @var array
     */
    private static $composers = [];

    /*
     * Plugin constructor.
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->router = $app->make(Router::class);
        $this->gate = $app->make(GateContract::class);
    }

    /*
     * Plugin bootstrap
     * Called in system boot
     */
    public function boot()
    {

        foreach ($this->getMiddlewares() as $middleware) {
            $this->router->pushMiddlewareToGroup("web", $middleware);
        }

        foreach ($this->getRouteMiddlewares() as $alias => $middleware) {
            $this->router->aliasMiddleware($alias, $middleware);
        }

        if ($this->app->runningInConsole()) {
            $this->commands($this->getCommands());
        }

        $this->loadViewsFrom($this->getPath('views'), $this->getKey());
        $this->loadTranslationsFrom($this->getPath('lang'), $this->getKey());

        $this->loadMigrationsFrom($this->getRootPath('database/migrations'));

        if (file_exists($this->getRootPath('public'))) {
            $this->publishes([
                $this->getRootPath('public') => public_path("plugins/" . $this->getKey()),
            ], $this->getKey() . ".public");
        }

        if (file_exists($this->getRootPath('config'))) {
            $this->publishes([
                $this->getRootPath('config') => config_path(),
            ], $this->getKey() . ".config");
        }

        if (file_exists($this->getPath('views'))) {
            $this->publishes([
                $this->getPath('views') => resource_path('views/vendor/' . $this->getKey())
            ], $this->getKey() . ".views");
        }

        if (file_exists($routes = $this->getPath("routes.php"))) {
            require $routes;
        }
    }

    /*
     * Get plugin middlewares
     * @return mixed
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /*
     * Get plugin route middlewares
     * @return mixed
     */
    public function getRouteMiddlewares()
    {
        return $this->route_middlewares;
    }

    /*
     * Get plugin commands
     * @return mixed
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /*
     * Get plugin absolute path
     * @param string $path
     * @return string
     */
    public function getPath($path = NULL)
    {
        return $path ? $this->path . DIRECTORY_SEPARATOR . $path : $this->path;
    }

    /*
     * Get plugin key
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /*
     * Get plugin absolute root path
     * @param string $path
     * @return string
     */
    public function getRootPath($path = NULL)
    {
        return $path ? dirname($this->path) . DIRECTORY_SEPARATOR . $path : dirname($this->path);
    }

    /*
     * Plugin registration
     * Extending core classes
     */
    public function register()
    {

        if (file_exists($config = $this->getRootPath() . '/config/' . $this->getKey() . '.php')) {
            $this->mergeConfigFrom(
                $config, $this->getKey()
            );
        }

        foreach ($this->getProviders() as $provider) {
            $this->app->register($provider);
        }

        foreach ($this->getAliases() as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }
    }

    /*
     * Get plugin providers
     * @return mixed
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /*
     * Get plugin aliases
     * @return mixed
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /*
     * Plugin install hook
     * Running plugin migrations and default options
     * @param $command
     */
    public function install($command)
    {

        $command->call("vendor:publish", [
            "--tag" => [$this->getKey() . ".public"],
            "--force" => true
        ]);

        $command->call("vendor:publish", [
            "--tag" => [$this->getKey() . ".config"],
        ]);

        $command->call("plugin:migrate", [
            "plugin" => $this->getKey()
        ]);
    }

    /*
     * Plugin uninstall hook
     * Rollback plugin installation
     * @param $command
     */
    public function uninstall($command)
    {
        // do any thing
    }

    /*
     * Get plugin relative path
     * @param string $path
     * @return string
     */
    public function getRelativePath($path = NULL)
    {
        return str_replace(base_path(), "", $this->getPath($path));
    }

    /*
     * Get plugin relative root path
     * @param string $path
     * @return string
     */
    public function getRelativeRootPath($path = NULL)
    {
        return str_replace(base_path(), "", $this->getRootPath($path));
    }

    /*
     * Get plugin key
     * @return mixed
     */
    public function getClassName()
    {
        return $this->class;
    }

    /*
     * Get plugin key
     * @return mixed
     */
    public function getClassFileName()
    {
        return $this->class_file_name;
    }

    /*
     * Get recursive plugin dependencies
     * @return array
     */
    public function getRecursiveDependencies()
    {
        return array_merge($this->getDependencies(), PluginFacade::getRecursive($this));
    }

    /*
     * Get plugin dependencies
     * @return mixed
     */
    public function getDependencies()
    {

        $dependencies = [];

        foreach ($this->dependencies as $key => $class) {
            $dependencies[$key] = PluginFacade::get($key, $class);
        }

        return $dependencies;
    }

    /*
     * Get plugin permissions
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /*
     * Get plugin description
     * @return mixed
     */
    public function getDescription()
    {
        return $this->composer()->description ?? NULL;
    }

    /*
     * Get decoded plugin composer file
     * @return mixed
     */
    private function composer()
    {

        if (!array_key_exists($this->getKey(), self::$composers) and file_exists(dirname($this->getPath()) . "/composer.json")) {
            self::$composers[$this->getKey()] = json_decode(file_get_contents(dirname($this->getPath()) . "/composer.json"));
        } else {
            self::$composers[$this->getKey()] = new \stdClass();
        }

        return self::$composers[$this->getKey()];
    }

    /*
     * Get plugin version
     * @return mixed
     */
    public function getVersion()
    {

        $composer_lock_file = base_path("composer.lock");

        if (file_exists($composer_lock_file)) {

            $composer_lock_content = json_decode(file_get_contents($composer_lock_file));

            $packages = $composer_lock_content->packages;

            $package = collect($packages)->where("name", $this->getName())->first();

            if ($package) {
                return $package->version;
            }
        }

        return "dev-master";
    }

    /*
     * Get plugin commands
     * @return mixed
     */
    public function getName()
    {
        return $this->composer()->name ?? NULL;
    }

    /*
     * Get plugin license
     * @return mixed
     */
    public function getLicense()
    {
        return $this->composer()->license ?? NULL;
    }

    /*
     * Convert class into string
     * @return string
     */
    public function __toString()
    {
        return $this->getKey();
    }

}
