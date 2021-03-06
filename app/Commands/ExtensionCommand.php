<?php

namespace Admin\Commands;

use Admin\Commands\BaseCommand\BaseExtension;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ExtensionCommand
 * @package Admin\Commands
 */
class ExtensionCommand extends BaseExtension
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'admin:extension';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bfg Admin extension manager';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (is_numeric($name) && $find = $this->all_extensions()->where('id', $name)->first()) {

            $name = $find['name'];
        }

        if ($name) {

            if ($this->option('edit')) {

                return $this->edit_extension($name);
            }

            if ($this->option('make')) {

                return $this->make_extension($name);
            }

            return $this->work_with_extension($name);
        }

        else if ($this->option('install')) {

            return $this->install_all();
        }

        else if ($this->option('uninstall')) {

            return $this->uninstall_all();
        }

        else if ($this->option('update')) {

            return $this->update_all();
        }

        else if ($this->option('reinstall')) {

            return $this->reinstall_all();
        }

        else if ($this->option('show')) {

            return $this->remote_list();
        }

        else {

            return $this->installed_list();
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, 'Select name of extension'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['show', 's', InputOption::VALUE_NONE, 'Show all existing packages'],
            ['install', 'i', InputOption::VALUE_NONE, 'Install any selected or all extensions'],
            ['update', 'u', InputOption::VALUE_NONE, 'Update any selected or all extensions'],
            ['reinstall', 'r', InputOption::VALUE_NONE, 'ReInstall any selected or all extension'],
            ['uninstall', 'd', InputOption::VALUE_NONE, 'UnInstall any selected or all extensions'],
            ['force', 'f', InputOption::VALUE_NONE, 'Force action'],

            ['yes', 'y', InputOption::VALUE_NONE, 'Enter yes on all'],

            ['make', 'm', InputOption::VALUE_NONE, 'Create extension by selected name'],
            ['edit', 'e', InputOption::VALUE_NONE, 'Edit extension by selected name']
        ];
    }
}
