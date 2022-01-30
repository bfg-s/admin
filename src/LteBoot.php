<?php

namespace LteAdmin;

use Illuminate\Support\Facades\Event;
use LteAdmin\Components\ModelTableComponent;
use LteAdmin\Core\PageMixin;
use LteAdmin\Core\TableExtends\Decorations;
use LteAdmin\Core\TableExtends\Display;
use LteAdmin\Core\TableExtends\Editables;
use LteAdmin\Core\TableExtends\Formatter;
use LteAdmin\Core\TaggableComponent;
use ReflectionException;

class LteBoot
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected static $listen = [

    ];

    /**
     * Table extensions.
     * @var array
     */
    protected static $table_classes = [
        Editables::class,
        Formatter::class,
        Decorations::class,
        Display::class,
    ];

    /**
     * Run boot Lte scripts.
     * @throws ReflectionException
     */
    public static function run()
    {
        foreach (static::$listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }

        TaggableComponent::create();

        Page::mixin(new PageMixin);

        include __DIR__.'/bootstrap.php';

        foreach (static::$table_classes as $item) {
            ModelTableComponent::addExtensionClass($item);
        }

        foreach (\LteAdmin::extensions() as $extension) {
            if ($extension->included()) {
                $extension->config()->boot();
            }
        }

        if (!app()->runningInConsole()) {
            gets()->lte->menu->save_current_query();
        }
    }
}
