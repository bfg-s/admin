<?php

declare(strict_types=1);

namespace Admin;

use Admin\Components\ModelTableComponent;
use Admin\Core\TableExtends\Decorations;
use Admin\Core\TableExtends\Display;
use Admin\Core\TableExtends\Editables;
use Admin\Core\TableExtends\Formatter;
use ReflectionException;

class Boot
{
    /**
     * Table extensions.
     * @var array
     */
    protected static array $table_classes = [
        Editables::class,
        Formatter::class,
        Decorations::class,
        Display::class,
    ];

    /**
     * Run boot scripts.
     * @throws ReflectionException
     */
    public static function run(): void
    {
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
