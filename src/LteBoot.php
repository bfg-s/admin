<?php

namespace Lar\LteAdmin;

use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Core\TableExtends\Decorations;
use Lar\LteAdmin\Core\TableExtends\Display;
use Lar\LteAdmin\Core\TableExtends\Editables;
use Lar\LteAdmin\Core\TableExtends\Formatter;
use Lar\LteAdmin\Core\TagableComponent;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\Tagable\Field;
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
     */
    public static function run()
    {
        /**
         * Register tagable components
         */
        TagableComponent::create();

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
        static::makeGates();

        //Component::injectCollection(Field::$form_components);
    }

    /**
     * Make gates for controller
     */
    protected static function makeGates()
    {
        if (!\Route::current()) {

            return ;
        }

        $route_action = trim(\Route::currentRouteAction(), '\\');

        $action = \Str::parseCallback($route_action);

        /** @var LteFunction $item */
        foreach (LteFunction::with('roles')->where('class', $action[0])->get() as $item) {

            \Gate::define("{$action[0]}@{$item->slug}", function (LteUser $user) use ($item) {
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
