<?php

namespace Admin\Extension\Providers;

use Admin\Extension\Extension;
use Illuminate\Console\Command;

/**
 * Class UpdateProvider
 * @package Admin\Extension\Providers
 */
class UpdateProvider {

    /**
     * @var Command
     */
    public $command;

    /**
     * @var Extension
     */
    public $provider;

    /**
     * UpdateProvider constructor.
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
}