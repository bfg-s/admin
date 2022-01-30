<?php

namespace LteAdmin\Traits;

use Closure;
use LteAdmin\Controllers\AdministratorsController;
use LteAdmin\Controllers\FunctionsController;
use LteAdmin\Controllers\PermissionController;
use LteAdmin\Controllers\RolesController;
use LteAdmin\Core\NavGroup;
use LteAdmin\Core\NavItem;

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
}
