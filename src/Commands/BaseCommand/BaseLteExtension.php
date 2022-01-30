<?php

namespace LteAdmin\Commands\BaseCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lar\Layout\CfgFile;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;
use LteAdmin\LteAdmin;

class BaseLteExtension extends Command
{
    use LteExtensionTrait;

    /**
     * @var string
     */
    protected $remote_url = 'https://packagist.org/packages/list.json?type=lar-lte-admin-extension';

    /**
     * @param $name
     * @return null
     */
    protected function edit_extension($name)
    {
        if (!isset(LteAdmin::$installed_extensions[$name]) && !isset(LteAdmin::$not_installed_extensions[$name])) {
            $this->error("Extension [$name] for edit not found!");

            return null;
        }

        $from = base_path("vendor/{$name}");
        $to = lte_app_path("Extensions/{$name}");

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

        $this->add_repo_to_composer(str_replace(base_path().'/', '', $to).'/');

        $this->call_composer("require {$name}");

        $this->info("Extension [{$name}] moved to [{$to}]");

        return $this->choiceDone();
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

        $base_dir = lte_app_path("Extensions/{$name}");

        foreach (
            [
                $base_dir.'/src/Extension',
                $base_dir.'/migrations',
                $base_dir.'/views',
            ] as $dir
        ) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, 1);
                $this->info('Created dir ['.str_replace(base_path(), '', realpath($dir)).']!');
            }
        }

        foreach (
            [
                $base_dir.'/views/.gitkeep' => '',
                $base_dir.'/migrations/.gitkeep' => '',
                $base_dir.'/composer.json' => $this->get_stub('composer'),
                $base_dir.'/README.md' => $this->get_stub('README'),
                $base_dir.'/src/helpers.php' => $this->get_stub('helpers'),
                $base_dir.'/src/ServiceProvider.php' => $this->get_stub('ServiceProvider'),
                $base_dir.'/src/Extension/Config.php' => $this->get_stub('Config'),
                $base_dir.'/src/Extension/Install.php' => $this->get_stub('Install'),
                $base_dir.'/src/Extension/Uninstall.php' => $this->get_stub('Uninstall'),
                $base_dir.'/src/Extension/Navigator.php' => $this->get_stub('Navigator'),
                $base_dir.'/src/Extension/Permissions.php' => $this->get_stub('Permissions'),
            ] as $file => $file_data
        ) {
            if (!is_file($file)) {
                file_put_contents($file, $file_data);
                $this->info('Created file ['.str_replace(base_path(), '', realpath($file)).']!');
            }
        }

        $this->add_repo_to_composer(str_replace(base_path().'/', '', $base_dir).'/');

        $this->call_composer("require {$name}");

        $this->info("Extension [{$name}] created!");

        return $this->choiceDone();
    }

    /**
     * Install all extensions.
     */
    protected function install_all()
    {
        $this->info('Run install all extensions...');

        foreach (LteAdmin::$not_installed_extensions as $not_installed_extension) {
            $this->choiceInstall($not_installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * @param $name
     */
    protected function choiceInstall($name)
    {
        $this->info("Run install [$name]...");
        if (isset(LteAdmin::$not_installed_extensions[$name])) {
            LteAdmin::$not_installed_extensions[$name]->install($this);
            LteAdmin::$not_installed_extensions[$name]->permission($this, 'up');
            CfgFile::open(storage_path('lte_extensions.php'))->write($name, true);
            $this->info("Extension [$name] installed!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * UnInstall all extensions.
     */
    protected function uninstall_all()
    {
        $this->info('Run uninstall all extensions...');

        foreach (LteAdmin::$installed_extensions as $installed_extension) {
            $this->choiceUninstall($installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * @param $name
     */
    protected function choiceUninstall($name)
    {
        $this->info("Run uninstall [$name]...");
        if (isset(LteAdmin::$installed_extensions[$name])) {
            LteAdmin::$installed_extensions[$name]->permission($this, 'down');
            LteAdmin::$installed_extensions[$name]->uninstall($this);
            CfgFile::open(storage_path('lte_extensions.php'))->remove($name);
            $this->info("Extension [$name] uninstalled!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * ReInstall all extensions.
     */
    protected function reinstall_all()
    {
        $this->info('Run reinstall all extensions...');

        foreach (LteAdmin::$installed_extensions as $installed_extension) {
            $this->choiceReinstall($installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * @param $name
     */
    protected function choiceReinstall($name)
    {
        if (isset(LteAdmin::$installed_extensions[$name])) {
            $this->info("Run reinstall [$name]...");
            LteAdmin::$installed_extensions[$name]->permission($this, 'down');
            LteAdmin::$installed_extensions[$name]->uninstall($this);
            LteAdmin::$installed_extensions[$name]->install($this);
            LteAdmin::$installed_extensions[$name]->permission($this, 'up');
            $this->info("Extension [$name] reinstalled!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * @param $name
     */
    protected function work_with_extension($name)
    {
        $choice = 'Done';

        if (isset(LteAdmin::$installed_extensions[$name])) {
            if ($this->option('reinstall')) {
                $choice = 'Reinstall';
            } elseif ($this->option('uninstall')) {
                $choice = 'Uninstall';
            } else {
                $choice = $this->choice("Extension [{$name}] is installed!", [
                    'Done',
                    (LteAdmin::$extensions[$name] ? 'Disable' : 'Enable'),
                    'Reinstall',
                    'Uninstall',
                ], 0);
            }
        } elseif (isset(LteAdmin::$not_installed_extensions[$name])) {
            if (!$this->option('install')) {
                $choice = $this->choice("Extension [{$name}] is NOT installed!", [
                    'Done',
                    'Install',
                ], 0);
            } else {
                $choice = 'Install';
            }
        } else {
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
        } else {
            $filter_list = collect($list)->filter(static function ($ext) use ($name) {
                return strpos($ext, $name) !== false;
            })->filter(static function ($ext) {
                return !isset(LteAdmin::$installed_extensions[$ext]) &&
                    !isset(LteAdmin::$not_installed_extensions[$ext]);
            });

            if (!$filter_list->count()) {
                $this->error("Extensions by keyword [$name] not found!");
            } elseif ($filter_list->count() === 1) {
                return $this->download_extension($filter_list->first(), $this->option('install'));
            } else {
                $download = $this->choice('Find extension, select for download', $filter_list->toArray());

                return $this->download_extension($download, true);
            }
        }

        return $this->choiceDone();
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

        $v = '';

        if (count($versions) === 1) {
            $v = ' '.$versions[0];
        } else {
            $v = ' '.$versions[1];
        }

        $this->call_composer("require {$name}{$v}");

        $this->info("Extension [{$name}] downloaded!");

        return $this->choiceDone();
    }

    /**
     * @param $name
     */
    protected function choiceEnable($name)
    {
        if (isset(LteAdmin::$installed_extensions[$name])) {
            CfgFile::open(storage_path('lte_extensions.php'))->write($name, true);
            $this->info("Extension [$name] enabled!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * @param $name
     */
    protected function choiceDisable($name)
    {
        if (isset(LteAdmin::$installed_extensions[$name])) {
            CfgFile::open(storage_path('lte_extensions.php'))->write($name, false);
            $this->info("Extension [$name] disabled!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * Get and show all remote extensions.
     */
    protected function remote_list()
    {
        $list = $this->getRemotes();

        if (count($list)) {
            $all = collect();

            foreach ($list as $name) {
                $all->push([
                    'name' => $name,
                    'status' => isset(LteAdmin::$extensions[$name]) ? (LteAdmin::$extensions[$name] ? '<info>Enabled</info>' : '<comment>Disabled</comment>') : '<comment>Not installed</comment>',
                    'downloaded' => isset(LteAdmin::$installed_extensions[$name]) || isset(LteAdmin::$not_installed_extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>',
                    'installed' => isset(LteAdmin::$installed_extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>',
                ]);
            }

            if (!$this->option('install')) {
                $this->line('');
                $this->info('All remote extensions on packagist.org:');
                $this->table(['Name', 'Status', 'Downloaded', 'Installed'], $all->sortBy('name')->toArray());
            } else {
                $ch = collect($list)->filter(static function ($ext) {
                    return !isset(LteAdmin::$installed_extensions[$ext]) &&
                        !isset(LteAdmin::$not_installed_extensions[$ext]);
                })->toArray();

                if (!count($ch)) {
                    $this->error('Not found packages for install!');

                    return $this->choiceDone();
                }

                $download = $this->choice('All extensions, select for download', $ch);

                return $this->download_extension($download, true);
            }
        }

        return null;
    }

    /**
     * Show downloaded extension list.
     */
    protected function installed_list()
    {
        $all = $this->all_extensions();

        if (!$all->count()) {
            $this->error('Not found any downloaded packages');

            return $this->choiceDone();
        }

        $this->line('');
        $this->info('Downloaded LteAdmin list extensions:');
        $this->table(['ID', 'Name', 'Description', 'Status', 'Installed'], $all->toArray());

        return null;
    }

    /**
     * @return Collection
     */
    protected function all_extensions()
    {
        return collect(array_merge(LteAdmin::$installed_extensions, LteAdmin::$not_installed_extensions))
            ->filter(static function ($extension) {
                return $extension::$slug !== 'application';
            })
            ->values()
            ->map(static function ($extension, $key) {
                $name = $extension::$name;

                return [
                    'id' => $key + 1,
                    'name' => $name,
                    'desc' => lang_in_text($extension::$description),
                    'status' => isset(LteAdmin::$extensions[$name]) ? (LteAdmin::$extensions[$name] ? '<info>Enabled</info>' : '<comment>Disabled</comment>') : '<comment>Not installed</comment>',
                    'installed' => isset(LteAdmin::$extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>',
                ];
            })->sortBy('name');
    }
}
