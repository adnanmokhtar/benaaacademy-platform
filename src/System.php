<?php

namespace Dot\Platform;

use Dot\Options\Facades\Option;
use Dot\Platform\Facades\Dot;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/*
 * Class System
 * System Core class
 * @package Dot\Platform
 */
class System extends Plugin
{

    protected $dependencies = [
        "options" => \Dot\Options\Options::class,
        "auth" => \Dot\Auth\Auth::class,
        "media" => \Dot\Media\Media::class,
        "dashboard" => \Dot\Dashboard\Dashboard::class,
    ];

    /*
     * System providers
     * @var array
     */
    protected $aliases = [

        /*
         * Laravel aliases
         */
        'Str' => \Illuminate\Support\Str::class,

        /*
         * Dot aliases
         */
        'Dot' => \Dot\Platform\Facades\Dot::class,
        'Plugin' => \Dot\Platform\Facades\Plugin::class,
        'Navigation' => \Dot\Platform\Facades\Navigation::class,
        'Action' => \Dot\Platform\Facades\Action::class,
        'Widget' => \Dot\Platform\Facades\Widget::class,
        'Schedule' => \Dot\Platform\Facades\Schedule::class,
    ];

    /*
     * System commands
     * @var array
     */
    protected $commands = [

        // Dot commands
        \Dot\Platform\Commands\DotInstallCommand::class,
        \Dot\Platform\Commands\DotUpdateCommand::class,
        \Dot\Platform\Commands\DotMigrateCommand::class,
        \Dot\Platform\Commands\DotUserCommand::class,

        // Plugins commands
        \Dot\Platform\Commands\PluginListCommand::class,
        \Dot\Platform\Commands\PluginMigrationCommand::class,
        \Dot\Platform\Commands\PluginMigrateCommand::class,
        \Dot\Platform\Commands\PluginInstallCommand::class,
        \Dot\Platform\Commands\PluginUninstallCommand::class,
        \Dot\Platform\Commands\PluginPublishCommand::class

    ];

    /*
     * System middlewares
     * @var array
     */
    protected $middlewares = [
        \Dot\Platform\Middlewares\PlatformMiddleware::class
    ];

    /*
     * system bootstrap
     * Called in system boot
     */
    public function boot()
    {
        define("DOT_VERSION", Dot::version());
        define("ADMIN", config("admin.prefix"));
        define("API", config("admin.api"));

        /*
         * Auto detecting website domain.
         */

        if (Config::get("app.url") == "http://localhost") {
            Config::set("app.url", Request::root());
        }

        /*
         * Debugging
         */

        if (Config::get("app.debug")) {
            app()->register(\Barryvdh\Debugbar\ServiceProvider::class);
            DB::connection('mysql')->enableQueryLog();
        }

        /*
         * Detecting guard
         */

        if (Request::is(ADMIN . "/*")) {
            define("GUARD", "backend");
        } elseif (Request::is(API . "/*")) {
            define("GUARD", "api");
        } else {
            define("GUARD", "frontend");
        }

        require_once $this->getPath('overrides.php');
        require_once $this->getPath('helpers.php');

        Option::page("general", function ($option) {
            $option->title(trans("admin::options.general_options"))
                ->icon("fa-sliders")
                ->order(0)
                ->view("admin::options");
        });

        $this->publishes([
            $this->getPath('views/errors') => resource_path('views/errors')
        ], $this->getKey() . ".errors");

        parent::boot();
    }

    /*
     *  Registering services
     */
    function register()
    {
        @date_default_timezone_set(config("app.timezone"));
        parent::register();
    }

    function install($command)
    {
        parent::install($command);

        $command->call("vendor:publish", [
            "--tag" => $this->getKey() . ".errors"
        ]);

        $command->info("Setting default options");

        // Set plugin default options

        Option::set("site_name", "Site Name", 1);
        Option::set("site_slogan", "Site Slogan", 1);
        Option::set("site_email", "dot@platform.com", 0);
        Option::set("site_copyrights", "All rights reserved", 1);
        Option::set("site_timezone", "Etc/GMT", 0);
        Option::set("site_date_format", "relative", 0);
        Option::set("site_status", 1, 0);
        Option::set("site_offline_message", NULL, 1);
    }

    function uninstall($command)
    {
        parent::uninstall($command);

        $command->info("Deleting default options");

        // Delete plugin options

        Option::delete("site_name");
        Option::delete("site_slogan");
        Option::delete("site_email");
        Option::delete("site_copyrights");
        Option::delete("site_timezone");
        Option::delete("site_date_format");
        Option::delete("site_status");
        Option::delete("site_offline_message");
    }


}
