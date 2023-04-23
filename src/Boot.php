<?php

namespace Admin;

use Illuminate\Support\Facades\Event;
use Admin\Components\ModelTableComponent;
use Admin\Core\PageMixin;
use Admin\Core\TableExtends\Decorations;
use Admin\Core\TableExtends\Display;
use Admin\Core\TableExtends\Editables;
use Admin\Core\TableExtends\Formatter;
use Admin\Core\TaggableComponent;
use ReflectionException;

class Boot
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
     * Run boot scripts.
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

        Page::mixin(new PageMixin());

        include __DIR__.'/bootstrap.php';

        foreach (static::$table_classes as $item) {
            ModelTableComponent::addExtensionClass($item);
        }

        foreach (\Admin\Facades\AdminFacade::extensions() as $extension) {
            if ($extension->included()) {
                $extension->config()->boot();
            }
        }

        if (!app()->runningInConsole()) {
            admin_repo()->saveCurrentQuery;
        }
    }
}
