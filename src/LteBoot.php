<?php

namespace Lar\LteAdmin;

use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Core\LtePageMixin;
use Lar\LteAdmin\Core\TableExtends\Decorations;
use Lar\LteAdmin\Core\TableExtends\Display;
use Lar\LteAdmin\Core\TableExtends\Editables;
use Lar\LteAdmin\Core\TableExtends\Formatter;
use Lar\LteAdmin\Core\TaggableComponent;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\LtePage;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

/**
 * Class LteBoot
 * @package Lar\LteAdmin
 */
class LteBoot
{
    /**
     * Table extensions
     * @var array
     */
    protected static $table_classes = [
        Editables::class,
        Formatter::class,
        Decorations::class,
        Display::class
    ];

    /**
     * Run boot Lte scripts
     * @throws \ReflectionException
     */
    public static function run()
    {
        /**
         * Register tagable components
         */
        TaggableComponent::create();

        LtePage::mixin(new LtePageMixin);

        include __DIR__ . '/bootstrap.php';

        foreach (static::$table_classes as $item) {

            ModelTable::addExtensionClass($item);
        }

        foreach (\LteAdmin::extensions() as $extension) {

            if ($extension->included()) {

                $extension->config()->boot();
            }
        }

        static::formMacros();

        if (!app()->runningInConsole() && \Schema::hasTable('lte_functions')) {

            static::makeGates();
        }
    }

    /**
     * Make gates for controller
     */
    protected static function makeGates()
    {
        /** @var LteFunction $item */
        foreach (LteFunction::with('roles')->where('active', 1)->get() as $item) {

            \Gate::define("{$item->class}@{$item->slug}", function (LteUser $user) use ($item) {
                return $user->hasRoles($item->roles->pluck('slug')->toArray());
            });
        }
    }

    /**
     * Make helper form
     */
    protected static function formMacros()
    {
        Form::macro('info_at', function ($condition = null) {
            if ($condition === null) $condition = gets()->lte->menu->type === 'edit';
            if ($condition) $this->hr();
            $this->if($condition)->info('updated_at', 'lte.updated_at');
            $this->if($condition)->info('created_at', 'lte.created_at');
        });

        Form::macro('info_id', function ($condition = null) {
            if ($condition === null) $condition = gets()->lte->menu->type === 'edit';
            $this->if($condition)->info('id', 'lte.id');
        });
    }
}
