<?php

namespace Lar\LteAdmin\Commands;

use Illuminate\Console\Command;
use Lar\Layout\CfgFile;
use Lar\LteAdmin\LteAdmin;
use Lar\LteAdmin\Models\LteUser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeUser
 *
 * @package Lar\Admin\Commands
 */
class LteExtension extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'lte:extension';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lte Admin extension manager';

    /**
     * @var string
     */
    protected $remote_url = "https://packagist.org/packages/list.json?type=lar-lte-admin-extension";

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
        if ($this->option('remote')) {

            return $this->remote_list();
        }

        $name = $this->argument('name');

        if ($name) {

            return $this->work_with_extension($name);
        }

        else {

            return $this->installed_list();
        }
    }

    /**
     * @param $name
     */
    protected function work_with_extension($name)
    {
        $choice = "Done";

        if (isset(LteAdmin::$installed_extensions[$name])) {

            $choice = $this->choice("Extension [{$name}] is installed!", [
                'Done',
                (LteAdmin::$extensions[$name] ? 'Disable' : 'Enable'),
                'Reinstall',
                'Uninstall'
            ], 0);
        }

        else if (isset(LteAdmin::$not_installed_extensions[$name])) {

            $choice = $this->choice("Extension [{$name}] is NOT installed!", [
                'Done',
                'Install'
            ], 0);
        }

        else {

            return $this->find_remote($name);
        }

        return $this->{"choice{$choice}"}($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function find_remote($name)
    {
        $list = $this->getRemotes();

        if (array_search($name, $list) !== false) {

            return $this->download_extension($name);
        }

        else {

            $filter_list = collect($list)->filter(function ($ext) use ($name) {
                return strpos($ext, $name) !== false;
            })->filter(function ($ext) {
                return !isset(LteAdmin::$installed_extensions[$ext]) &&
                    !isset(LteAdmin::$not_installed_extensions[$ext]);
            });

            if (!$filter_list->count()) {

                $this->error("Extensions by keyword [$name] not found!");
            }

            else if ($filter_list->count() === 1) {

                return $this->download_extension($filter_list->first());
            }

            else {

                $download = $this->choice("Find extension, select for download", $filter_list->toArray());

                return $this->download_extension($download);
            }
        }

        return $this->choiceDone();
    }

    /**
     * @param $name
     */
    protected function download_extension($name)
    {
        $data = file_get_contents("https://packagist.org/packages/{$name}.json");
        $data = json_decode($data, 1);
        if (isset($data['status']) && $data['status'] === 'error') {
            $this->error($data['message']);
            return ;
        }
        $package = $data['package'];

        dd('Download: '.$name, $package);
    }

    /**
     * @param $name
     */
    protected function choiceEnable($name) {
        if (isset(LteAdmin::$installed_extensions[$name])) {
            CfgFile::open(storage_path('lte_extensions.php'))->write($name, true);
            $this->info("Extension [$name] enabled!");
            return $this->choiceDone();
        }
        $this->error("Extension [$name] not found!");
    }

    /**
     * @param $name
     */
    protected function choiceDisable($name) {
        if (isset(LteAdmin::$installed_extensions[$name])) {
            CfgFile::open(storage_path('lte_extensions.php'))->write($name, false);
            $this->info("Extension [$name] disabled!");
            return $this->choiceDone();
        }
        $this->error("Extension [$name] not found!");
    }

    /**
     * @param $name
     */
    protected function choiceReinstall($name) {

        $this->info("Run reinstall [$name]...");
        if (isset(LteAdmin::$installed_extensions[$name])) {
            $this->info("Use uninstall...");
            LteAdmin::$installed_extensions[$name]->uninstall($this);
            $this->info("Uninstalled!");
            $this->info("Use install...");
            LteAdmin::$installed_extensions[$name]->install($this);
            $this->info("Extension [$name] reinstalled!");
            return $this->choiceDone();
        }
        $this->error("Extension [$name] not found!");
    }

    /**
     * @param $name
     */
    protected function choiceUninstall($name) {

        $this->info("Run uninstall [$name]...");
        if (isset(LteAdmin::$installed_extensions[$name])) {
            LteAdmin::$installed_extensions[$name]->uninstall($this);
            CfgFile::open(storage_path('lte_extensions.php'))->remove($name);
            $this->info("Extension [$name] uninstalled!");
            return $this->choiceDone();
        }
        $this->error("Extension [$name] not found!");
    }

    /**
     * @param $name
     */
    protected function choiceInstall($name) {

        $this->info("Run install [$name]...");
        if (isset(LteAdmin::$not_installed_extensions[$name])) {
            LteAdmin::$not_installed_extensions[$name]->install($this);
            CfgFile::open(storage_path('lte_extensions.php'))->write($name, true);
            $this->info("Extension [$name] installed!");
            return $this->choiceDone();
        }
        $this->error("Extension [$name] not found!");
    }

    /**
     * @param $name
     */
    protected function choiceDone($name = null)
    {
        $this->info('By!');
        return ;
    }

    /**
     * Get and show all remote extensions
     */
    protected function remote_list()
    {
        $list = $this->getRemotes();

        if (count($list)) {

            $all = collect();

            foreach ($list as $name) {

                $all->push([
                    'name' => $name,
                    'status' => isset(LteAdmin::$extensions[$name]) ? (LteAdmin::$extensions[$name] ? '<info>Enabled</info>' : '<comment>Disabled</comment>') : '<error>No</error>',
                    'downloaded' => isset(LteAdmin::$installed_extensions[$name]) || isset(LteAdmin::$not_installed_extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>',
                    'installed' => isset(LteAdmin::$installed_extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>'
                ]);
            }

            $this->line('');
            $this->info("All remote extensions on packagist.org:");
            $this->table(['Name', 'Downloaded', 'Installed'], $all->sortBy('name')->toArray());
        }
    }

    /**
     * Show downloaded extension list
     */
    protected function installed_list()
    {
        $all = collect();

        foreach (
            array_merge(LteAdmin::$installed_extensions, LteAdmin::$not_installed_extensions) as
            $name => $extension
        ) {
            $all->push([
                'name' => $name,
                'desc' => $extension::$description,
                'status' => isset(LteAdmin::$extensions[$name]) ? (LteAdmin::$extensions[$name] ? '<info>Enabled</info>' : '<comment>Disabled</comment>') : '<error>No</error>',
                'installed' => isset(LteAdmin::$extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>'
            ]);
        }

        $this->line('');
        $this->info("Downloaded LteAdmin list extensions:");
        $this->table(['Name', 'Description', 'Status', 'Installed'], $all->sortBy('name')->toArray());
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, 'Name of extension for install ou uninstall'],
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
            ['remote', 'r', InputOption::VALUE_NONE, 'Show all remote extensions'],
        ];
    }

    /**
     * @return array
     */
    protected function getRemotes()
    {
        $list = file_get_contents($this->remote_url);

        $list = json_decode($list, 1);

        return $list['packageNames'];
    }
}
