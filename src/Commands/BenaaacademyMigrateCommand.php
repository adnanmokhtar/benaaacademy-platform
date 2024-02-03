<?php

namespace Benaaacademy\Platform\Commands;

use Benaaacademy\Platform\Command;
use Benaaacademy\Platform\Facades\Plugin;
use Illuminate\Container\Container;

/*
 * Class MigrateCommand
 */
class BenaaacademyMigrateCommand extends Command
{

    /*
     * @var string
     */
    protected $name = 'Benaaacademy:migrate';

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
