<?php

namespace Admin\Commands\BaseCommand;

use Admin\Extension\Extension;
use Bfg\Dev\ConfigSaver;
use Illuminate\Console\Command;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

/**
 * Class BaseExtension
 * @package Admin\Commands\BaseCommand
 */
class BaseExtension extends Command
{
    use ExtensionTrait;

    /**
     * @var string
     */
    protected $remote_url = "https://packagist.org/packages/list.json?type=bfg-admin-extension";

    /**
     * @param $name
     * @return null
     */
    protected function edit_extension($name)
    {
        if (!\AdminExtension::has($name)) {

            $this->error("Extension [$name] for edit not found!");
            return null;
        }

        $from = base_path("vendor/{$name}");
        $to = admin_app_path("Extensions/{$name}");

        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to' => new Flysystem(new LocalAdapter($to)),
        ]);

        foreach ($manager->listContents('from://', true) as $file) {
            if ($file['type'] === 'file') {
                $manager->put('to://'.$file['path'], $manager->read('from://'.$file['path']));
            }
        }

        $this->call_composer("remove {$name}");

        $this->add_repo_to_composer(str_replace(base_path().'/', '', $to) . '/');

        $this->call_composer("require {$name}");

        $this->info("Extension [{$name}] moved to [{$to}]");

        return $this->choiceDone();
    }

    /**
     * @param $name
     * @return null
     */
    protected function make_extension($name)
    {
        if (!$this->validate_new_extension_name($name)) {

            $this->error("Invalidate name [{$name}]! Must be - {folder}/{extension-name}");

            return $this->choiceDone();
        }

        if ($this->any_exists_extension($name)) {

            $this->error("Name [{$name}] already exists!");

            return $this->choiceDone();
        }

        $this->enterDescription();

        $base_dir = admin_app_path("Extensions/{$name}");

        foreach ([
             $base_dir.'/app/Extension',
             $base_dir.'/resources/js',
             $base_dir.'/resources/css',
             $base_dir.'/public',
        ] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, 1);
                $this->info("Created dir [".str_replace(base_path(), '', realpath($dir))."]!");
            }
        }

        foreach ([
            //$base_dir.'/views/.gitkeep' => '',
            $base_dir.'/public/.gitkeep' => '',
            $base_dir.'/.gitignore' => $this->get_stub('gitignore'),
            $base_dir.'/package.json' => $this->get_stub('package_json'),
            $base_dir.'/resources/js/app.js' => '',
            $base_dir.'/resources/css/app.css' => '',
            $base_dir.'/composer.json' => $this->get_stub('composer'),
            $base_dir.'/README.md' => $this->get_stub('README'),
            $base_dir.'/app/helpers.php' => $this->get_stub('helpers'),
            $base_dir.'/app/ServiceProvider.php' => $this->get_stub('ServiceProvider'),
            $base_dir.'/app/Extension/Config.php' => $this->get_stub('Config'),
            $base_dir.'/app/Extension/Install.php' => $this->get_stub('Install'),
            $base_dir.'/app/Extension/Update.php' => $this->get_stub('Update'),
            $base_dir.'/app/Extension/Uninstall.php' => $this->get_stub('Uninstall')
        ] as $file => $file_data) {
            if (!is_file($file)) {
                file_put_contents($file, $file_data);
                $this->info("Created file [".str_replace(base_path(), '', realpath($file))."]!");
            }
        }

        $this->add_repo_to_composer(str_replace(base_path().'/', '', $base_dir) . '/');

        $this->call_composer("require {$name}");

        $this->info("Extension [{$name}] created!");

        return $this->choiceDone();
    }

    /**
     * Install all extensions
     */
    protected function install_all()
    {
        foreach (\AdminExtension::notInstalled() as $not_installed_extension) {

            $this->choiceInstall($not_installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * UnInstall all extensions
     */
    protected function uninstall_all()
    {
        foreach (\AdminExtension::extensions() as $installed_extension) {

            $this->choiceUninstall($installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * Update all extensions
     */
    protected function update_all()
    {
        foreach (\AdminExtension::extensions() as $installed_extension) {

            $this->choiceUpdate($installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * ReInstall all extensions
     */
    protected function reinstall_all()
    {
        foreach (\AdminExtension::extensions() as $installed_extension) {

            $this->choiceReinstall($installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * @param $name
     */
    protected function work_with_extension($name)
    {
        $choice = "Done";

        if (\AdminExtension::isInstalled($name)) {

            if ($this->option('reinstall')) {
                $choice = "Reinstall";
            } else if ($this->option('uninstall')) {
                $choice = "Uninstall";
            } else if ($this->option('update')) {
                $choice = "Update";
            } else {
                $choice = $this->choice("Extension [{$name}] is installed!", [
                    'Done',
                    (\AdminExtension::isIncluded($name) ? 'Disable' : 'Enable'),
                    'Update',
                    'Reinstall',
                    'Uninstall'
                ], 0);
            }
        }

        else if (\AdminExtension::isNotInstalled($name)) {

            if (!$this->option('install')) {
                $choice = $this->choice("Extension [{$name}] is NOT installed!", [
                    'Done',
                    'Install'
                ], 0);
            } else {
                $choice = 'Install';
            }
        }

        else {

            return $this->find_remote($name);
        }

        $this->{"choice{$choice}"}($name);

        return $this->choiceDone();
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function find_remote($name)
    {
        $list = $this->getRemotes();

        if (array_search($name, $list) !== false) {

            return $this->download_extension($name, $this->option('install'));
        }

        else {

            $filter_list = collect($list)->filter(function ($ext) use ($name) {
                return strpos($ext, $name) !== false;
            })->filter(function ($ext) {
                return !\AdminExtension::has($ext);
            });

            if (!$filter_list->count()) {

                $this->error("Extensions by keyword [$name] not found!");
            }

            else if ($filter_list->count() === 1) {

                return $this->download_extension($filter_list->first(), $this->option('install'));
            }

            else {

                $download = $this->choice("Find extension, select for download", $filter_list->toArray());

                return $this->download_extension($download, true);
            }
        }

        return $this->choiceDone();
    }

    /**
     * @param $name
     * @param  bool  $auto
     */
    protected function download_extension($name, $auto = false)
    {
        if (!$auto) {

            if (!$this->confirm("Download extension [$name]?", true)) {

                return null;
            }
        }

        $data = file_get_contents("https://packagist.org/packages/{$name}.json");
        $data = json_decode($data, 1);
        if (isset($data['status']) && $data['status'] === 'error') {
            $this->error($data['message']);
            return null;
        }
        $package = $data['package'];
        $versions = array_keys($package['versions']);

        $this->info("Download [{$name}]...");

        $v = "";

        if (count($versions) === 1) { $v = " " . $versions[0]; }
        else { $v = " " . $versions[1]; }

        $this->call_composer("require {$name}{$v}");

        $this->info("Extension [{$name}] downloaded!");

        return $this->choiceDone();
    }

    /**
     * @param $name
     */
    protected function choiceEnable($name) {
        if (\AdminExtension::isInstalled($name)) {
            ConfigSaver::open(storage_path('admin_extensions.php'))->write($name, true);
            $this->info("Extension [$name] enabled!");
            return null;
        }
        $this->error("Extension [$name] not found!");
        return null;
    }

    /**
     * @param $name
     */
    protected function choiceDisable($name) {
        if (\AdminExtension::isInstalled($name)) {
            ConfigSaver::open(storage_path('admin_extensions.php'))->write($name, false);
            $this->info("Extension [$name] disabled!");
            return null;
        }
        $this->error("Extension [$name] not found!");
        return null;
    }

    /**
     * @param $name
     */
    protected function choiceReinstall($name) {

        if (\AdminExtension::isInstalled($name)) {
            $this->info("Extension [$name] reinstallation...");
            \AdminExtension::get($name)->uninstall($this);
            \AdminExtension::get($name)->install($this);
            $this->info("Extension [$name] reinstalled!");
            return null;
        }
        $this->error("Extension [$name] not found!");
        return null;
    }

    /**
     * @param $name
     */
    protected function choiceUpdate($name) {

        if (\AdminExtension::isInstalled($name)) {
            $this->info("Extension [$name] updating...");
            \AdminExtension::get($name)->update($this);
            $this->info("Extension [$name] updated!");
            return null;
        }
        $this->error("Extension [$name] not found!");
        return null;
    }

    /**
     * @param $name
     */
    protected function choiceUninstall($name) {

        if (\AdminExtension::isInstalled($name)) {
            $this->info("Extension [$name] uninstalling...");
            \AdminExtension::get($name)->uninstall($this);
            ConfigSaver::open(storage_path('admin_extensions.php'))->remove($name);
            $this->info("Extension [$name] uninstalled!");
            return null;
        }
        $this->error("Extension [$name] not found!");
        return null;
    }

    /**
     * @param $name
     */
    protected function choiceInstall($name) {

        if (\AdminExtension::isNotInstalled($name)) {
            $this->info("Extension [$name] installing...");
            \AdminExtension::get($name)->install($this);
            ConfigSaver::open(storage_path('admin_extensions.php'))->write($name, true);
            $this->info("Extension [$name] installed!");
            return null;
        }
        $this->error("Extension [$name] not found!");
        return null;
    }

    /**
     * @param $name
     * @return null
     */
    protected function choiceDone($name = null)
    {
        return null;
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
                    'status' => \AdminExtension::has($name) ? (\AdminExtension::isIncluded($name) ? '<info>Enabled</info>' : '<comment>Disabled</comment>') : '<comment>Not installed</comment>',
                    'downloaded' => \AdminExtension::has($name) ? '<info>Yes</info>' : '<comment>No</comment>',
                    'installed' => \AdminExtension::isIncluded($name) ? '<info>Yes</info>' : '<comment>No</comment>'
                ]);
            }

            if (!$this->option('install')) {

                $this->line('');
                $this->info("All remote extensions on packagist.org:");
                $this->table(['Name', 'Status', 'Downloaded', 'Installed'], $all->sortBy('name')->toArray());
            }

            else {

                $ch = collect($list)->filter(function ($ext) {
                    return !\AdminExtension::has($ext);
                })->toArray();

                if (!count($ch)) {
                    $this->error('Not found packages for install!');
                    return $this->choiceDone();
                }

                $download = $this->choice("All extensions, select for download", $ch);
                return $this->download_extension($download, true);
            }
        }

        return null;
    }

    /**
     * Show downloaded extension list
     */
    protected function installed_list()
    {
        $all = $this->all_extensions();

        if (!$all->count()) {

            $this->error('Not found any downloaded packages');
            return $this->choiceDone();
        }

        $this->line('');
        $this->info("Downloaded Bfg Admin list extensions:");
        $this->table(['ID', 'Name', 'Description', 'Status', 'Installed'], $all->toArray());
        return null;
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

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function all_extensions()
    {
        return collect(\AdminExtension::getAll())
            //->filter(function (Extension $extension) { return $extension::$slug !== 'application'; })
            ->values()
            ->map(function ($extension, $key) {
                $name = $extension::$name;
                return [
                    'id' => $key+1,
                    'name' => $name,
                    'desc' => lang_in_text($extension::$description),
                    'status' => \AdminExtension::has($name) ? (\AdminExtension::isProviderIncluded($name) ? '<info>Enabled</info>' : '<comment>Disabled</comment>') : '<comment>Not installed</comment>',
                    'installed' => \AdminExtension::has($name) ? '<info>Yes</info>' : '<comment>No</comment>'
                ];
            })->sortBy('name');
    }
}