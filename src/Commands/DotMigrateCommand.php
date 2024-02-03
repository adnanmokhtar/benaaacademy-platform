<?php

namespace Dot\Platform\Commands;

use Dot\Platform\Command;
use Dot\Platform\Facades\Plugin;
use Illuminate\Container\Container;

/*
 * Class MigrateCommand
 */
class DotMigrateCommand extends Command
{

    /*
     * @var string
     */
    protected $name = 'dot:migrate';

    /*
     * @var string
     */
    protected $description = "Migrate all system migration files";

    /*
     * @param Container $app
     */
    public function handle(Container $app)
    {
        foreach (Plugin::all() as $plugin) {

            if (file_exists($plugin->getRootPath() . "/database/migrations")) {

                $this->line("- " . ucfirst($plugin->getKey()) . " Plugin");

                $this->call('plugin:migrate', [
                    'plugin' => $plugin->getKey()
                ]);

                $this->info("\n");
            }
        }
    }

}
