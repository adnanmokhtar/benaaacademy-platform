<?php

namespace Dot\Platform\Commands;

use Dot\Platform\Command;
use Dot\Platform\Facades\Plugin;

/*
 * Class MigrateCommand
 */
class PluginListCommand extends Command
{

    /*
     * @var string
     */
    protected $name = 'plugin:list';

    /*
     * @var string
     */
    protected $description = "List all plugins";

    /*
     *
     */
    public function handle()
    {

        $plugins = Plugin::all();

        $rows = [];

        foreach ($plugins as $plugin) {
            $rows[] = [
                $plugin->getKey(),
                $plugin->getName(),
                $plugin->getVersion(),
                $plugin->getdescription(),
                $plugin->getLicense(),
            ];
        }

        $this->table(
            ["Key", "Name", "Version", "Description", "License"],
            $rows
        );
    }


}
