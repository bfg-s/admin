<?php

declare(strict_types=1);

namespace Admin\Interfaces;

use Illuminate\Console\Command;

/**
 * Generator assistant interface for creating assistants.
 */
interface AdminHelpGeneratorInterface
{
    /**
     * Handle call method.
     *
     * @param  Command  $command
     * @return mixed
     */
    public function handle(Command $command): mixed;
}
