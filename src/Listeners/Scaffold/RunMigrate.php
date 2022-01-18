<?php

namespace Lar\LteAdmin\Listeners\Scaffold;

use Lar\LteAdmin\Events\Scaffold;

/**
 * Class RunMigrate.
 * @package App\Listeners\Lar\LteAdmin\Listeners\Scaffold
 */
class RunMigrate
{
    /**
     * Handle the event.
     *
     * @param  Scaffold  $event
     * @return void
     */
    public function handle(Scaffold $event)
    {
        if ($event->create['migration'] && $event->create['migrate']) {
            \Artisan::call('migrate');

            respond()->toast_success('Migration performed!');
        }
    }
}
