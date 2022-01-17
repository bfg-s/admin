<?php

namespace Lar\LteAdmin\Listeners\Scaffold;

use Lar\LteAdmin\Events\Scaffold;
use Lar\LteAdmin\Models\LteFunction;

/**
 * Class CreateController
 * @package App\Listeners\Lar\LteAdmin\Listeners\Scaffold
 */
class CreateControllerPermissions
{
    /**
     * Handle the event.
     *
     * @param  Scaffold  $event
     * @return void
     */
    public function handle(Scaffold $event)
    {
        if ($event->create['controller_permissions']) {

            $insert = [
                ['slug' => 'index', 'description' => '[GET] Model data list'],
                ['slug' => 'create', 'description' => '[GET] Model create form'],
                ['slug' => 'store', 'description' => '[POST] Model new data'],
                ['slug' => 'show', 'description' => '[GET] Model show data'],
                ['slug' => 'edit', 'description' => '[GET] Model edit form'],
                ['slug' => 'update', 'description' => '[PUT/PATCH] Model update data'],
                ['slug' => 'destroy', 'description' => '[DELETE] Model delete data'],
                ['slug' => 'restore', 'description' => '[DELETE] Model restore deleted data'],
                ['slug' => 'force_destroy', 'description' => '[DELETE] Model force delete data']
            ];

            foreach ($insert as $item) {
                LteFunction::firstOrNew(['class' => $event->controller, 'slug' => $item['slug']], $item);
            }
        }
    }
}
