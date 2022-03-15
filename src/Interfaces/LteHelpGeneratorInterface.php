<?php

namespace LteAdmin\Interfaces;

use Illuminate\Console\Command;

interface LteHelpGeneratorInterface
{
    /**
     * Handle call method.
     *
     * @param  Command  $command
     * @return mixed
     */
    public function handle(Command $command);
}
