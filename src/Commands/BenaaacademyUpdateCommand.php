<?php

namespace Benaaacademy\Platform\Commands;

use Benaaacademy\Platform\Command;
use Benaaacademy\Platform\Facades\Plugin;
use Symfony\Component\Console\Input\InputOption;

class BenaaacademyUpdateCommand extends Command
{

    /*
     * @var string
     */
    protected $name = 'Benaaacademy:update';

    /*
     * @var string
     */
    protected $description = "Updating files";

    /*
     * @return bool
     */
    public function handle()
    {

        foreach (Plugin::all() as $plugin) {
            $plugin->install($plugin->getKey());
        }

        $this->call('optimize', [
            '--quiet' => true
        ]);

        $this->info("Congratulations, Benaaacademy platform is now up to date!");
        $this->info("Platform version: " . Benaaacademy::version());

    }

    /*
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force overwrite config files', null]
        ];
    }

}
