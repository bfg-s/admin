<?php

namespace Lar\LteAdmin\Core\Traits;

use Closure;
use Lar\LteAdmin\Controllers\AdministratorsController;
use Lar\LteAdmin\Controllers\FunctionsController;
use Lar\LteAdmin\Controllers\PermissionController;
use Lar\LteAdmin\Controllers\RolesController;
use Lar\LteAdmin\Core\NavGroup;
use Lar\LteAdmin\Core\NavItem;

trait NavDefaultTools
{
    /**
     * Make auto default tools.
     * @return $this
     */
    public function makeDefaults()
    {
        $this->lteAdministrationGroup(static function (NavGroup $group) {
            $group->lteAdministrators();
            $group->lteRoles();
            $group->ltePermission();
            $group->lteFunctions();
        });

        return $this;
    }

    /**
     * Make default administration group.
     * @param  Closure|array  $call
     * @return NavGroup
     */
    public function lteAdministrationGroup($call)
    {
        return $this->group('lte.administration', 'admin', static function (NavGroup $group) use ($call) {
            if (is_embedded_call($call)) {
                call_user_func($call, $group);
            }
        })->icon_cogs();
    }

    /**
     * Make administrator list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function lteAdministrators(string $action = null)
    {
        return $this->item('lte.administrators', 'administrators')
            ->resource('lte_user', $action ?? AdministratorsController::class)
            ->icon_users_cog();
    }

    /**
     * Make default access group.
     * @param  Closure|array  $call
     * @return NavGroup
     */
    public function lteAccessGroup($call)
    {
        return $this->group('lte.access', 'access', static function (NavGroup $group) use ($call) {
            if (is_embedded_call($call)) {
                call_user_func($call, $group);
            }
        })->icon_universal_access();
    }

    /**
     * Make roles list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function lteRoles(string $action = null)
    {
        return $this->item('lte.roles', 'roles')
            ->resource('lte_role', $action ?? RolesController::class)
            ->icon_user_secret();
    }

    /**
     * Make permissions list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function ltePermission(string $action = null)
    {
        return $this->item('lte.permission', 'permission')
            ->resource('lte_permission', $action ?? PermissionController::class)
            ->icon_ban();
    }

    /**
     * Make functions/gates list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function lteFunctions(string $action = null)
    {
        return $this->item('lte.functions', 'functions')
            ->resource('lte_functions', $action ?? FunctionsController::class)
            ->icon_dungeon()->role('root');
    }
}
