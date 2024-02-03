<?php

namespace Dot\Platform\Commands;

use Dot\Platform\Command;
use Dot\Platform\Facades\Plugin;

/*
 * Class MigrateCommand
 */
class PluginMigrateCommand extends Command
{

    /*
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugin:migrate {plugin}
                {--database= : The database connection to use.}
                {--force : Force the operation to run when in production.}
                {--pretend : Dump the SQL queries that would be run.}
                {--seed : Indicates if the seed task should be re-run.}
                {--step : Force the migrations to be run so they can be rolled back individually.}';

    /*
     * @var string
     */
    protected $description = "Migrate plugin migration files";


    public function handle()
    {

        $plugin = Plugin::get($this->argument('plugin'));

        if (!$plugin) {
            return $this->error("Plugin " . $this->argument('plugin') . " not found");
        }

        $this->call('migrate', [
            '--path' => get_relative_path($plugin->getRootPath()) . "/database/migrations",
            '--force' => $this->option("force"),
            '--pretend' => $this->option("pretend"),
            '--seed' => $this->option("seed"),
            '--step' => $this->option("step")
        ]);
    }

}
