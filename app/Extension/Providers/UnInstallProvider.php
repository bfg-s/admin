<?php

namespace Admin\Extension\Providers;

use Admin\Extension\Extension;
use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migration;

/**
 * Class UnInstallProvider
 * @package Admin\Extension\Providers
 */
class UnInstallProvider {

    /**
     * @var Command
     */
    public $command;

    /**
     * @var Extension
     */
    public $provider;

    /**
     * UnInstallProvider constructor.
     * @param  Command  $command
     * @param  Extension  $provider
     */
    public function __construct(Command $command, Extension $provider)
    {
        $this->command = $command;
        $this->provider = $provider;
    }

    /**
     * @return void
     */
    public function handle(): void {

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
}