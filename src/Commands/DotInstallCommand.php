<?php

namespace Dot\Platform\Commands;

use Dot\Platform\Command;
use Dot\Platform\Facades\Plugin;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/*
 * Class DotInstallCommand
 *
 * @package Dot\Platform\Commands
 */
class DotInstallCommand extends Command
{

    /*
     * @var string
     */
    protected $name = 'dot:install';

    /*
     * @var string
     */
    protected $description = "Installing system";


    function __construct()
    {
        parent::__construct();
        $this->isInstalled = $this->isInstalled();
    }

    protected function isInstalled()
    {

        try {

            return Schema::hasTable("options");

        } catch (QueryException $exception) {
            return false;
        }

    }

    /*
     * @return bool
     */
    public function handle()
    {

        $this->line("\r");
        $this->line('<fg=black;bg=green> Dot Platform Installation </>');
        $this->line("\r");

        if(empty(config("app.key"))){
            $this->info('Generating application key');
            $this->call("key:generate");
        }

        $this->info('Generating optimized autoload files');

        @exec("composer dump-autoload -q");

        $this->line("\r");
        $this->line('<fg=black;bg=green> Checking system requirements </>');
        $this->line("\r");

        File::makeDirectory(public_path("uploads"), 0777, true, true);
        File::makeDirectory(public_path("plugins"), 0775, true, true);

        $this->setPermission(base_path("/storage"), 0777);
        $this->setPermission(base_path("/bootstrap/cache"), 0777);
        $this->setPermission(public_path("uploads"), 0777);
        $this->setPermission(public_path("plugins"), 0777);

        $server_errors = [];
        $server_messages = [];

        // Check php version

        $minimum_php = '7.0.0';

        if (version_compare(PHP_VERSION, $minimum_php, '>=')) {
            $server_messages[] = "PHP: " . PHP_VERSION;
        } else {
            $server_errors[] = "Please update your php to $minimum_php current is " . PHP_VERSION . ".";
        }

        // Check laravel version

        $minimum_laravel = '5.5';
        $laravel_version = app()->version();

        if (version_compare($laravel_version, $minimum_laravel, '>=')) {
            $server_messages[] = "Laravel: " . $laravel_version . ".";
        } else {
            $server_errors[] = "You must have laravel $minimum_laravel or higher." . " Current is " . $laravel_version;
        }

        // Check CURL extension is installed

        if (function_exists('curl_init')) {
            $server_messages[] = "PHP CURL extension is installed.";
        } else {
            $server_errors[] = "PHP CURL extension is not installed.";
        }

        // Check GD library is installed

        if (extension_loaded('gd') && function_exists('gd_info')) {
            $server_messages[] = "PHP GD library is installed.";
        } else {
            $server_errors[] = "PHP GD library is not installed.";
        }

        // Check storage is writable

        if (!is_writable($storage_path = base_path("storage"))) {
            $server_errors[] = "Storage path $storage_path is not writable.";
        } else {
            $server_messages[] = "Storage path $storage_path is writable.";
        }

        // Check cache is writable

        if (!is_writable($cache_path = base_path("bootstrap/cache"))) {
            $server_errors[] = "Cache path $cache_path is not writable";
        } else {
            $server_messages[] = "Cache path $cache_path is writable.";
        }

        // Check uploads is writable

        if (!is_writable($uploads_path = public_path("uploads"))) {
            $server_errors[] = "Uploads path $uploads_path is not writable.";
        } else {
            $server_messages[] = "Uploads path $uploads_path is writable.";
        }

        foreach ($server_messages as $message) {
            $this->line("<bg=green;fg=black> Passed: </> " . $message);
        }

        if (count($server_errors)) {

            foreach ($server_errors as $error) {
                $this->line("<bg=red;fg=white> Error:  </> " . $error);
            }

            $this->line("\r");

            $this->line("<fg=red>Please fix these error(s) before install.</>");
            $this->line("\r");

            return false;
        }

        $this->info("\r");

        $this->line("<fg=black;bg=green> Installing system </>");

        $this->info("\r");

        $this->line("<fg=green>Installing application migrations</>");

        $this->info("\r");

        $this->call("migrate", [
            '--quiet' => true,
            '--path' => get_relative_path(database_path("migrations"))
        ]);

        $this->info("\r");

        $this->info("Installing system plugins");

        $this->info("\r");

        foreach (config("admin.plugins") as $key => $class) {

            $plugin = Plugin::get($key);

            if ($plugin) {

                $plugins = array_merge($plugin->getRecursiveDependencies(), [$plugin]);

                foreach ($plugins as $plugin) {

                    $this->line("<fg=yellow>Installing: </>" . $plugin->getName());

                    $plugin->install($this);

                    $this->info("\r");
                }
            }
        }


        if (!$this->isInstalled) {

            $this->call("dot:user", [
                '--quiet' => true
            ]);

            $this->info("\r");

            $this->info("Congratulations, Dot Platform " . Plugin::get("admin")->getVersion() . " is now installed!");
            $this->info("Navigate to /" . config("admin.prefix") . " to browse the backend.");
            $this->info("Enjoy :)");

        }
    }

    /*
     * @param $path
     * @param $permission
     */
    function setPermission($path, $permission)
    {
        @chmod($path, $permission);
    }

}
