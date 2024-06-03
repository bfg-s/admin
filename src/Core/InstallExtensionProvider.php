<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\ExtendProvider;
use Illuminate\Console\Command;

/**
 * Part of the kernel that is responsible for installing admin panel extensions.
 */
class InstallExtensionProvider
{
    /**
     * The current class of the command that executes the installation.
     *
     * @var Command
     */
    public Command $command;

    /**
     * The extension's current service provider class.
     *
     * @var ExtendProvider
     */
    public ExtendProvider $provider;

    /**
     * InstallExtensionProvider constructor.
     *
     * @param  Command  $command
     * @param  ExtendProvider  $provider
     */
    public function __construct(Command $command, ExtendProvider $provider)
    {
        $this->command = $command;
        $this->provider = $provider;
    }

    /**
     * Method for handling the installation process of the admin panel extension.
     *
     * @return void
     */
    public function handle(): void
    {
    }
}
