<?php

namespace Lar\LteAdmin\Core\Generators;

use Illuminate\Console\Command;
use Lar\Developer\Commands\Dump\DumpExecute;
use Lar\LteAdmin\LteBoot;

/**
 * Class LteGeneratorBoot
 * @package Lar\LteAdmin\Core\Generators
 */
class LteGeneratorBoot implements DumpExecute {

    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        LteBoot::run();
    }
}