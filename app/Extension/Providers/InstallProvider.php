<?php

namespace Admin\Extension\Providers;

use Admin\Extension\Extension;
use Illuminate\Console\Command;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

/**
 * Class InstallProvider
 * @package Admin\Extension\Providers
 */
class InstallProvider {

    /**
     * @var Command
     */
    public $command;

    /**
     * @var Extension
     */
    public $provider;

    /**
     * InstallExtensionProvider constructor.
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
     * @param  string|array  $from
     * @param  string  $to
     * @param  bool  $force
     * @return bool
     * @throws \League\Flysystem\FileNotFoundException
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

                \File::makeDirectory($directory, 0755, true);
            }

            \File::copy($from, $to);

            $status('File');

        } elseif (is_dir($from)) {

            $manager = new MountManager([
                'from' => new Flysystem(new LocalAdapter($from)),
                'to' => new Flysystem(new LocalAdapter($to)),
            ]);

            foreach ($manager->listContents('from://', true) as $file) {
                if ($file['type'] === 'file' && (! $manager->has('to://'.$file['path']) || $force)) {
                    $manager->put('to://'.$file['path'], $manager->read('from://'.$file['path']));
                }
            }

            $status('Directory');
        }

        else {

            return false;
        }

        return true;
    }
}