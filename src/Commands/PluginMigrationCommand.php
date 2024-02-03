<?php

namespace Dot\Platform\Commands;

use Dot\Platform\Command;
use Dot\Platform\Facades\Plugin;

class PluginMigrationCommand extends Command
{

    /*
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'plugin:migration {name : The name of the migration.}
        {plugin : The name of the plugin.}
        {--create= : The table to be created.}
        {--table= : The table to migrate.}';

    /*
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new plugin migration file';


    /*
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

        $plugin = Plugin::get($this->argument('plugin'));

        if (!$plugin) {
            return $this->error("Plugin " . $this->argument('plugin') . " not found");
        }

        $this->call("make:migration", [
            "name" => $this->argument("name"),
            "--path" => get_relative_path($plugin->getRootPath()) . "/database/migrations",
            "--create" => $this->option("create"),
            "--table" => $this->option("table"),
        ]);
    }

}
