<?php

namespace Benaaacademy\Platform\Commands;

use Benaaacademy\Platform\Command;
use Benaaacademy\Platform\Facades\Plugin;
use Symfony\Component\Console\Input\InputArgument;


/*
 * Class MigrateCommand
 */
class PluginInstallCommand extends Command
{

    /*
     * @var string
     */
    protected $name = 'plugin:install';

    /*
     * @var string
     */
    protected $description = "Install plugin by name";

    /*
     *
     */
    public function handle()
    {

        $plugin = Plugin::get($this->argument('plugin'));

        if (!$plugin) {
            return $this->error("Plugin " . $this->argument('plugin') . " is not found or invalid");
        }

        $plugins = array_merge($plugin->getRecursiveDependencies(), [$plugin]);

        $this->info("\r");

        foreach ($plugins as $plugin) {

            $this->line("<fg=yellow>Installing: </>" . $plugin->getName());

            $plugin->install($this);

            $this->info("\r");
        }

    }

    /*
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::REQUIRED, 'The name of the plugin']
        ];
    }


}
