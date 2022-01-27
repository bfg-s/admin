<?php

namespace Lar\LteAdmin\Core;

use DB;
use File;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migration;
use Lar\LteAdmin\ExtendProvider;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;
use Symfony\Component\Finder\Finder;

class InstallExtensionProvider
{
    /**
     * @var Command
     */
    public $command;

    /**
     * @var ExtendProvider
     */
    public $provider;

    /**
     * InstallExtensionProvider constructor.
     * @param  Command  $command
     * @param  ExtendProvider  $provider
     */
    public function __construct(Command $command, ExtendProvider $provider)
    {
        $this->command = $command;
        $this->provider = $provider;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
    }

    /**
     * @param  string  $path
     * @param  bool  $publish
     * @return bool
     * @throws FileNotFoundException
     */
    public function migrate(string $path, bool $publish = true)
    {
        if ($publish) {
            $this->publish($path, database_path('migrations'));
        }

        if (is_dir($path)) {
            $files = iterator_to_array(Finder::create()
                ->files()
                ->ignoreDotFiles(false)
                ->in($path)
                ->depth(0)
                ->sortByName()
                ->reverseSorting(), false);

            if (!count($files)) {
                $this->command->info('Nothing to migrate.');

                return false;
            }

            foreach ($files as $file) {
                $migration_name = str_replace('.php', '', $file->getFilename());

                if (DB::table('migrations')->where('migration', $migration_name)->first()) {
                    continue;
                }

                $class = class_in_file($file->getPathname());

                if (!class_exists($class) && is_file(database_path('migrations/'.$file->getFilename()))) {
                    include database_path('migrations/'.$file->getFilename());
                }

                if (!class_exists($class)) {
                    include $file->getPathname();
                }

                if (!class_exists($class)) {
                    $this->command->line("<comment>Non-migration:</comment> {$migration_name}");
                    continue;
                }

                $migration = new $class;

                if ($migration instanceof Migration) {
                    if (method_exists($migration, 'up')) {
                        $this->command->line("<comment>Migrating:</comment> {$migration_name}");
                        $startTime = microtime(true);
                        $migration->up();
                        DB::table('migrations')->insert(['migration' => $migration_name, 'batch' => 1]);
                        $runTime = round(microtime(true) - $startTime, 2);
                        $this->command->line("<info>Migrated:</info>  {$migration_name} ({$runTime} seconds)");
                    } else {
                        $this->command->line("<comment>Non-migration:</comment> {$migration_name}");
                    }
                }
            }
        } else {
            $this->command->error("[{$path}] Is not directory");
            exit;
        }

        return true;
    }

    /**
     * @param  string|array  $from
     * @param  string  $to
     * @param  bool  $force
     * @return bool
     * @throws FileNotFoundException
     */
    public function publish($from, string $to = null, bool $force = false)
    {
        if (is_array($from)) {
            foreach ($from as $from_arr => $to_arr) {
                $this->publish($from_arr, $to_arr, $force);
            }

            return true;
        }

        $status = function ($type) use ($from, $to) {
            $from = str_replace(base_path(), '', realpath($from));

            $to = str_replace(base_path(), '', realpath($to));

            $this->command->line('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
        };

        if (is_file($from)) {
            $directory = dirname($to);

            if (!is_dir($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            File::copy($from, $to);

            $status('File');
        } elseif (is_dir($from)) {
            $manager = new MountManager([
                'from' => new Flysystem(new LocalAdapter($from)),
                'to' => new Flysystem(new LocalAdapter($to)),
            ]);

            foreach ($manager->listContents('from://', true) as $file) {
                if ($file['type'] === 'file' && (!$manager->has('to://'.$file['path']) || $force)) {
                    $manager->put('to://'.$file['path'], $manager->read('from://'.$file['path']));
                }
            }

            $status('Directory');
        } else {
            return false;
        }

        return true;
    }
}
