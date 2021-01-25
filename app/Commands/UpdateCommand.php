<?php

namespace Admin\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class UpdateCommand
 * @package Lar\LteAdmin\Commands
 */
class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'admin:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update BFG admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'admin-migrations',
            '--force' => true
        ]);

        $this->call('migrate', array_filter([
            '--force' => true
        ]));

        $this->call('vendor:publish', [
            '--tag' => 'admin-assets',
            '--force' => true
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'ui-assets',
            '--force' => true
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'admin-lang',
            '--force' => true
        ]);

        if (!is_file(config_path('admin.php'))) {

            $this->call('vendor:publish', [
                '--tag' => 'admin-config'
            ]);
        }

        $this->call('admin:extension', ['--update' => true]);

        $this->info("Bfg Admin Updated!");

        if ($this->option('migrate')) {

            $this->call('migrate', array_filter([
                '--force' => true
            ]));
        }

        return 0;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['migrate', 'm', InputOption::VALUE_NONE, 'Run migrate after update'],
        ];
    }
}
