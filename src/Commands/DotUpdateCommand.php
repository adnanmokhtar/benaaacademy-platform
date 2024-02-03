<?php

namespace Dot\Platform\Commands;

use Dot\Platform\Command;
use Dot\Platform\Facades\Plugin;
use Symfony\Component\Console\Input\InputOption;

class DotUpdateCommand extends Command
{

    /*
     * @var string
     */
    protected $name = 'dot:update';

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

        $this->info("Congratulations, Dot platform is now up to date!");
        $this->info("Platform version: " . Dot::version());

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
