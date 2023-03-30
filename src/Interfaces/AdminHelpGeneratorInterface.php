<?php

namespace Admin\Interfaces;

use Illuminate\Console\Command;

interface AdminHelpGeneratorInterface
{
    /**
     * Handle call method.
     *
     * @param  Command  $command
     * @return mixed
     */
    public function handle(Command $command);
}
