<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migration;
use Lar\LteAdmin\ExtendProvider;

/**
 * Class InstallExtensionProvider
 * @package Lar\LteAdmin\Core
 */
class UnInstallExtensionProvider {

    /**
     * @var Command
     */
    public $command;
    /**
     * @var ExtendProvider
     */
    public $provider;

    /**
     * UnInstallExtensionProvider constructor.
     * @param  Command  $command
     * @param  ExtendProvider  $provider
     */
    public function __construct(Command $command, ExtendProvider $provider)
    {
        $this->command = $command;
        $this->provider = $provider;
    }

    /**
     * @param  string|array  $where
     * @param  string|bool  $in
     * @return int
     */
    public function unpublish($where, $in = null)
    {
        $deleted = 0;

        if (is_array($where)) {

            foreach ($where as $where_arr => $in_arr) {

                $deleted += $this->unpublish($where_arr, $in_arr);
            }
            return $deleted;
        }

        $where_real_path = str_replace(base_path(), '', realpath($where));

        $in_real_path = str_replace(base_path(), '', realpath($in));

        if (is_file($where) && is_file($in)) {

            if (basename($where) === basename($in)) {

                try { unlink($in); $deleted++; } catch (\Exception $e) {}

                if ($deleted) {

                    $this->command->line("<info>Removed file</info> <comment>[{$in_real_path}]</comment> <info>how</info> <comment>[{$where_real_path}]</comment>");
                }
            }

        } else if (is_dir($where) && is_dir($in)) {

            $in_files = collect(\File::allFiles($in, true))->map(function (\Symfony\Component\Finder\SplFileInfo $info) { return ['relativePath' => $info->getRelativePathname(), 'pathname' => $info->getPathname()]; });

            $where_files = collect(\File::allFiles($where, true))->map(function (\Symfony\Component\Finder\SplFileInfo $info) { return ['relativePath' => $info->getRelativePathname(), 'pathname' => $info->getPathname()]; });

            foreach ($where_files as $where_file) {

                if ($in_file = $in_files->where('relativePath', $where_file['relativePath'])->first()) {

                    try { unlink($in_file['pathname']); $deleted++; } catch (\Exception $e) {}
                }
            }

            $this->command->line("<info>The cleaned directory</info> <comment>[{$in_real_path}]</comment> <info>from <comment>[{$deleted}]</comment> files of the directory</info> <comment>[{$where_real_path}]</comment>");
        }

        return $deleted;
    }

    /**
     * @param  string  $path
     * @param  bool  $drop_publish
     * @return bool
     */
    public function migrateRollback(string $path, bool $drop_publish = true)
    {
        if (is_dir($path)) {
            $files = \File::files($path);

            if (!count($files)) {
                $this->command->info('Nothing to rollback.');
                return false;
            }

            foreach ($files as $file) {
                $class = class_in_file($file->getPathname())['class'];

                if (!class_exists($class) && is_file(database_path("migrations/".$file->getFilename()))) {
                    include database_path("migrations/".$file->getFilename());
                }

                if (!class_exists($class)) {
                    include $file->getPathname();
                }

                $migration_name = str_replace('.php', '', $file->getFilename());

                if (!class_exists($class)) {
                    $this->command->line("<comment>Non-migration:</comment> {$migration_name}");
                    continue;
                }

                $migration = new $class;

                if ($migration instanceof Migration) {
                    if (method_exists($migration, 'ignore') && $migration->ignore()) {
                        $this->command->line("<comment>Ignored-migration:</comment> {$migration_name}");
                        continue;
                    }
                    if (method_exists($migration, 'down')) {
                        $this->command->line("<comment>Rolling back:</comment> {$migration_name}");
                        $startTime = microtime(true);
                        $migration->down();
                        \DB::table('migrations')->where('migration', $migration_name)->delete();
                        $runTime = round(microtime(true) - $startTime, 2);
                        $this->command->line("<info>Rolled back:</info>  {$migration_name} ({$runTime} seconds)");
                    } else {
                        $this->command->line("<comment>Non-migration:</comment> {$migration_name}");
                    }
                }
            }

            if ($drop_publish) {
                $this->unpublish($path, database_path('migrations'));
            }
        } else {
            $this->command->error("[{$path}] Is not directory");
            exit;
        }

        return true;
    }
}