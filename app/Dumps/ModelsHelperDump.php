<?php

namespace Admin\Dumps;

use Bfg\Dev\Interfaces\DumpExecuteInterface;
use Illuminate\Console\Command;

/**
 * Class ModelsHelperDump
 * @package Admin\Dumps
 */
class ModelsHelperDump implements DumpExecuteInterface {

    /**
     * @param  Command  $command
     * @return mixed|void
     */
    public function handle(Command $command)
    {
        \Artisan::call('ide-helper:models --write --dir ' . __DIR__ . '/../Models');
    }
}