<?php

namespace Lar\LteAdmin\Commands;

use Illuminate\Console\Command;

/**
 * Class LteUpdateAssets
 *
 * @package Lar\LteAdmin\Commands
 */
class LteRepublishAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lte:republish-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Republish admin LTE assets';
}
