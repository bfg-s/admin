<?php

declare(strict_types=1);

namespace Admin\Commands;

use Exception;
use Admin\AdminEngine;
use Admin\Core\ConfigurationFileWriter;
use Admin\Core\JsonFormatter;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\MountManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * This class is designed to process commands that manage extensions for the admin panel.
 */
class AdminExtensionCommand extends Command
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
    protected $description = 'Admin extension manager';

    /**
     * URL of the remote list package extension.
     *
     * @var string
     */
    protected string $remote_url = 'https://packagist.org/packages/list.json?type=bfg-admin-extension';

    /**
     * A description that the user must provide.
     *
     * @var string|null
     */
    protected string|null $desc = null;

    /**
     * The name of the author that the user must specify.
     *
     * @var string|null
     */
    protected string|null $author_name = null;

    /**
     * Author's email which must be specified by the user.
     *
     * @var string|null
     */
    protected string|null $author_email = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FilesystemException
     */
    public function handle(): mixed
    {
        $name = $this->argument('name');

        if (is_numeric($name) && $find = $this->allExtensions()->where('id', $name)->first()) {
            $name = $find['name'];
        }

        if ($name) {
            if ($this->option('edit')) {
                return $this->editExtension($name);
            }

            if ($this->option('make')) {
                return $this->makeExtension($name);
            }

            return $this->workWithExtension($name);
        } elseif ($this->option('install')) {
            return $this->installAll();
        } elseif ($this->option('uninstall')) {
            return $this->uninstallAll();
        } elseif ($this->option('reinstall')) {
            return $this->reinstallAll();
        } elseif ($this->option('show')) {
            return $this->remoteList();
        } else {
            return $this->installedList();
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
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
    protected function getOptions(): array
    {
        return [
            ['show', 's', InputOption::VALUE_NONE, 'Show all existing packages'],
            ['install', 'i', InputOption::VALUE_NONE, 'Install any selected or all extensions'],
            ['uninstall', 'u', InputOption::VALUE_NONE, 'UnInstall any selected or all extensions'],
            ['reinstall', 'r', InputOption::VALUE_NONE, 'ReInstall any selected or all extension'],
            ['force', 'f', InputOption::VALUE_NONE, 'Force action'],

            ['yes', 'y', InputOption::VALUE_NONE, 'Enter yes on all'],

            ['make', 'm', InputOption::VALUE_NONE, 'Create extension by selected name'],
            ['edit', 'e', InputOption::VALUE_NONE, 'Edit extension by selected name'],
        ];
    }

    /**
     * A function that is responsible for adding the path to the composer repository.
     *
     * @param  string  $path
     * @return bool
     * @throws Exception
     */
    protected function addRepoToComposer(string $path): bool
    {
        $base_composer = json_decode(file_get_contents(base_path('composer.json')), true);

        if (!isset($base_composer['repositories'])) {
            $base_composer['repositories'] = [];
        }

        if (!collect($base_composer['repositories'])->where('url', $path)->first()) {
            $base_composer['repositories'][] = ['type' => 'path', 'url' => $path];
            file_put_contents(
                base_path('composer.json'),
                JsonFormatter::format(json_encode($base_composer), false, true)
            );
            $this->info("> Add PATH [{$path}] to repository!");

            return true;
        }

        return false;
    }

    /**
     * A function that is responsible for calling composer commands.
     *
     * @param  string  $command
     * @return null
     */
    protected function callComposer(string $command)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->comment("> Use \"composer {$command}\" for finish!");
        } else {
            exec('cd '.base_path()." && composer {$command}");
        }

        return null;
    }

    /**
     * A function that checks whether an extension exists.
     *
     * @param $name
     * @return bool
     */
    protected function anyExistsExtension($name): bool
    {
        if (isset(AdminEngine::$installed_extensions[$name]) || isset(AdminEngine::$not_installed_extensions[$name])) {

            return true;
        }

        return in_array($name, $this->getRemotes());
    }

    /**
     * A function that validates the specified package name for an extension.
     *
     * @param $name
     * @return array|bool
     */
    protected function validateNewExtensionName($name): bool|array
    {
        $name_parts = explode('/', $name);

        $name_parts = array_diff($name_parts, [null, '', 'admin']);

        if (count($name_parts) !== 2) {
            return false;
        }

        if (is_dir(admin_app_path("Extensions/{$name}"))) {
            return false;
        }

        if (is_dir(base_path("vendor/{$name}"))) {
            return false;
        }

        return true;
    }

    /**
     * Function for enter description, author and email.
     *
     * @return void
     */
    protected function enterDescription(): void
    {
        if (!$this->desc) {
            while (!$this->desc) {
                $this->desc = $this->ask('Enter description of extension');
            }
        }
        if (!$this->author_name) {
            while (!$this->author_name) {
                $this->author_name = $this->ask('Enter author name of extension');
            }
        }
        if (!$this->author_email) {
            while (!$this->author_email) {
                $this->author_email = $this->ask('Enter author email of extension');
            }
        }
    }

    /**
     * Get a template stub.
     *
     * @param  string  $file
     * @return false|string
     */
    protected function getStub(string $file): bool|string
    {
        $data = file_get_contents(__DIR__."/Stubs/{$file}.stub");

        $name = $this->argument('name');

        list($folder, $extension) = explode('/', $name);

        $namespace = 'Admin\\'.ucfirst(Str::camel($folder !== 'bfg' ? $folder : 'extend')).'\\'.ucfirst(Str::camel($extension));

        $data = str_replace([
            '{NAME}', '{DESCRIPTION}', '{FOLDER}', '{EXTENSION}', '{ADMIN_VERSION}',
            '{COMPOSER_NAMESPACE}', '{NAMESPACE}', '{SLUG}',
            '{AUTHOR_NAME}', '{AUTHOR_EMAIL}'
        ], [
            $name, static::$desc, $folder, $extension, \Admin::version(),
            str_replace('\\', '\\\\', $namespace), $namespace,
            Str::slug(str_replace('/', '_', $name), '_'),
            static::$author_name, static::$author_email
        ], $data);

        return $data;
    }

    /**
     * Handles the edit action of an extension package.
     *
     * @param $name
     * @return null
     * @throws FilesystemException
     * @throws Exception
     */
    protected function editExtension($name)
    {
        if (!isset(AdminEngine::$installed_extensions[$name]) && !isset(AdminEngine::$not_installed_extensions[$name])) {
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

        $this->callComposer("remove {$name}");

        $this->addRepoToComposer(str_replace(base_path().'/', '', $to).'/');

        $this->callComposer("require {$name}");

        $this->info("Extension [{$name}] moved to [{$to}]");

        return $this->choiceDone();
    }

    /**
     * An event that fires every time any of the selected actions is completed.
     *
     * @param $name
     * @return null
     */
    protected function choiceDone($name = null)
    {
        return null;
    }

    /**
     * The action of creating a new extension package for the admin panel.
     *
     * @param $name
     * @return null
     * @throws Exception
     */
    protected function makeExtension($name)
    {
        if (!$this->validateNewExtensionName($name)) {
            $this->error("Invalidate name [{$name}]! Must be - {folder}/{extension-name}");

            return $this->choiceDone();
        }

        if ($this->anyExistsExtension($name)) {
            $this->error("Name [{$name}] already exists!");

            return $this->choiceDone();
        }

        $this->enterDescription();

        $base_dir = admin_app_path("Extensions/{$name}");

        foreach (
            [
                $base_dir.'/src/Extension',
                $base_dir.'/migrations',
                $base_dir.'/views',
            ] as $dir
        ) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
                $this->info('Created dir ['.str_replace(base_path(), '', realpath($dir)).']!');
            }
        }

        foreach (
            [
                $base_dir.'/views/.gitkeep' => '',
                $base_dir.'/migrations/.gitkeep' => '',
                $base_dir.'/composer.json' => $this->getStub('composer'),
                $base_dir.'/README.md' => $this->getStub('README'),
                $base_dir.'/LICENSE.md' => $this->getStub('LICENSE'),
                $base_dir.'/src/helpers.php' => $this->getStub('helpers'),
                $base_dir.'/src/ServiceProvider.php' => $this->getStub('ServiceProvider'),
                $base_dir.'/src/Extension/Config.php' => $this->getStub('Config'),
                $base_dir.'/src/Extension/Install.php' => $this->getStub('Install'),
                $base_dir.'/src/Extension/Uninstall.php' => $this->getStub('Uninstall'),
                $base_dir.'/src/Extension/Navigator.php' => $this->getStub('Navigator'),
                $base_dir.'/src/Extension/Permissions.php' => $this->getStub('Permissions'),
            ] as $file => $file_data
        ) {
            if (!is_file($file)) {
                file_put_contents($file, $file_data);
                $this->info('Created file ['.str_replace(base_path(), '', realpath($file)).']!');
            }
        }

        $this->addRepoToComposer(str_replace(base_path().'/', '', $base_dir).'/');

        $this->callComposer("require {$name}:\"dev-main\"");

        $this->info("Extension [{$name}] created!");

        return $this->choiceDone();
    }

    /**
     * The action of installing all package extensions.
     */
    protected function installAll()
    {
        $this->info('Run install all extensions...');

        foreach (AdminEngine::$not_installed_extensions as $not_installed_extension) {
            $this->choiceInstall($not_installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * The action to install the selected extension package.
     *
     * @param $name
     * @return null
     */
    protected function choiceInstall($name)
    {
        $this->info("Run install [$name]...");
        if (isset(AdminEngine::$not_installed_extensions[$name])) {
            AdminEngine::$not_installed_extensions[$name]->install($this);
            ConfigurationFileWriter::open(app()->bootstrapPath('admin_extensions.php'))->write($name, true);
            $this->info("Extension [$name] installed!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * The action of removing all extension packages.
     *
     * @return null
     */
    protected function uninstallAll()
    {
        $this->info('Run uninstall all extensions...');

        foreach (AdminEngine::$installed_extensions as $installed_extension) {
            $this->choiceUninstall($installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * The action to remove the specified extension package.
     *
     * @param $name
     * @return null
     */
    protected function choiceUninstall($name)
    {
        $this->info("Run uninstall [$name]...");
        if (isset(AdminEngine::$installed_extensions[$name])) {
            AdminEngine::$installed_extensions[$name]->uninstall($this);
            ConfigurationFileWriter::open(app()->bootstrapPath('admin_extensions.php'))->remove($name);
            $this->info("Extension [$name] uninstalled!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * The action of reinstalling all expansion packs.
     */
    protected function reinstallAll()
    {
        $this->info('Run reinstall all extensions...');

        foreach (AdminEngine::$installed_extensions as $installed_extension) {
            $this->choiceReinstall($installed_extension::$name);
        }

        return $this->choiceDone();
    }

    /**
     * The action to reinstall the specified extension package.
     *
     * @param $name
     * @return null
     */
    protected function choiceReinstall($name)
    {
        if (isset(AdminEngine::$installed_extensions[$name])) {
            $this->info("Run reinstall [$name]...");
            AdminEngine::$installed_extensions[$name]->uninstall($this);
            AdminEngine::$installed_extensions[$name]->install($this);
            $this->info("Extension [$name] reinstalled!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * The action of working with an extension package offers a choice of actions that can be performed with it.
     *
     * @param $name
     * @return mixed|null
     */
    protected function workWithExtension($name): mixed
    {
        $choice = 'Done';

        if (isset(AdminEngine::$installed_extensions[$name])) {
            if ($this->option('reinstall')) {
                $choice = 'Reinstall';
            } elseif ($this->option('uninstall')) {
                $choice = 'Uninstall';
            } else {
                $choice = $this->choice("Extension [{$name}] is installed!", [
                    'Done',
                    (AdminEngine::$extensions[$name] ? 'Disable' : 'Enable'),
                    'Reinstall',
                    'Uninstall',
                ], 0);
            }
        } elseif (isset(AdminEngine::$not_installed_extensions[$name])) {
            if (!$this->option('install')) {
                $choice = $this->choice("Extension [{$name}] is NOT installed!", [
                    'Done',
                    'Install',
                ], 0);
            } else {
                $choice = 'Install';
            }
        } else {
            return $this->findRemote($name);
        }

        $this->{"choice{$choice}"}($name);

        return $this->choiceDone();
    }

    /**
     * Action to find and install an extension package.
     *
     * @param $name
     * @return mixed
     */
    protected function findRemote($name): mixed
    {
        $list = $this->getRemotes();

        if (in_array($name, $list)) {
            return $this->downloadExtension($name, $this->option('install'));
        } else {
            $filter_list = collect($list)->filter(static function ($ext) use ($name) {
                return str_contains($ext, $name);
            })->filter(static function ($ext) {
                return !isset(AdminEngine::$installed_extensions[$ext]) &&
                    !isset(AdminEngine::$not_installed_extensions[$ext]);
            });

            if (!$filter_list->count()) {
                $this->error("Extensions by keyword [$name] not found!");
            } elseif ($filter_list->count() === 1) {
                return $this->downloadExtension($filter_list->first(), $this->option('install'));
            } else {
                $download = $this->choice('Find extension, select for download', $filter_list->toArray());

                return $this->downloadExtension($download, true);
            }
        }

        return $this->choiceDone();
    }

    /**
     * Get a remote list of extension packages.
     *
     * @return array
     */
    protected function getRemotes(): array
    {
        $list = file_get_contents($this->remote_url);

        $list = json_decode($list, true);

        return $list['packageNames'];
    }

    /**
     * Load the specified extension package from a remote source.
     *
     * @param $name
     * @param  bool  $auto
     * @return null
     */
    protected function downloadExtension($name, bool $auto = false)
    {
        if (!$auto) {
            if (!$this->confirm("Download extension [$name]?", true)) {
                return null;
            }
        }

        $data = file_get_contents("https://packagist.org/packages/{$name}.json");
        $data = json_decode($data, true);
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

        $this->callComposer("require {$name}{$v}");

        $this->info("Extension [{$name}] downloaded!");

        return $this->choiceDone();
    }

    /**
     * The action of enabling the specified extension package.
     *
     * @param $name
     * @return null
     */
    protected function choiceEnable($name)
    {
        if (isset(AdminEngine::$installed_extensions[$name])) {
            ConfigurationFileWriter::open(app()->bootstrapPath('admin_extensions.php'))->write($name, true);
            $this->info("Extension [$name] enabled!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * The action to disable the specified extension package.
     *
     * @param $name
     * @return null
     */
    protected function choiceDisable($name)
    {
        if (isset(AdminEngine::$installed_extensions[$name])) {
            ConfigurationFileWriter::open(app()->bootstrapPath('admin_extensions.php'))->write($name, false);
            $this->info("Extension [$name] disabled!");

            return null;
        }
        $this->error("Extension [$name] not found!");

        return null;
    }

    /**
     * Get and show all remote extensions.
     *
     * @return null
     */
    protected function remoteList()
    {
        $list = $this->getRemotes();

        if (count($list)) {
            $all = collect();

            foreach ($list as $name) {
                $all->push([
                    'name' => $name,
                    'status' => isset(AdminEngine::$extensions[$name]) ? (AdminEngine::$extensions[$name] ? '<info>Enabled</info>' : '<comment>Disabled</comment>') : '<comment>Not installed</comment>',
                    'downloaded' => isset(AdminEngine::$installed_extensions[$name]) || isset(AdminEngine::$not_installed_extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>',
                    'installed' => isset(AdminEngine::$installed_extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>',
                ]);
            }

            if (!$this->option('install')) {
                $this->line('');
                $this->info('All remote extensions on packagist.org:');
                $this->table(['Name', 'Status', 'Downloaded', 'Installed'], $all->sortBy('name')->toArray());
            } else {
                $ch = collect($list)->filter(static function ($ext) {
                    return !isset(AdminEngine::$installed_extensions[$ext]) &&
                        !isset(AdminEngine::$not_installed_extensions[$ext]);
                })->toArray();

                if (!count($ch)) {
                    $this->error('Not found packages for install!');

                    return $this->choiceDone();
                }

                $download = $this->choice('All extensions, select for download', $ch);

                return $this->downloadExtension($download, true);
            }
        }

        return null;
    }

    /**
     * Show installed extension packages.
     *
     * @return null
     */
    protected function installedList()
    {
        $all = $this->allExtensions();

        if (!$all->count()) {
            $this->error('Not found any downloaded packages');

            return $this->choiceDone();
        }

        $this->line('');
        $this->info('Downloaded Admin list extensions:');
        $this->table(['ID', 'Name', 'Description', 'Status', 'Installed'], $all->toArray());

        return null;
    }

    /**
     * Get a list of all installed and uninstalled extension packages.
     *
     * @return Collection
     */
    protected function allExtensions(): Collection
    {
        return collect(array_merge(AdminEngine::$installed_extensions, AdminEngine::$not_installed_extensions))
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
                    'status' => isset(AdminEngine::$extensions[$name]) ? (AdminEngine::$extensions[$name] ? '<info>Enabled</info>' : '<comment>Disabled</comment>') : '<comment>Not installed</comment>',
                    'installed' => isset(AdminEngine::$extensions[$name]) ? '<info>Yes</info>' : '<comment>No</comment>',
                ];
            })->sortBy('name');
    }
}
