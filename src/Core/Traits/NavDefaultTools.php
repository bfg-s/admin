<?php

namespace Lar\LteAdmin\Core\Traits;

use Lar\LteAdmin\Core\NavGroup;

/**
 * Trait NavDefaultToos
 * @package Lar\LteAdmin\Core\Traits
 */
trait NavDefaultTools
{
    /**
     * Make auto default tools
     * @return $this
     */
    public function makeDefaults()
    {
        $this->lteAdministrationGroup(function  (NavGroup $group) {

            $group->lteAdministrators();

            $group->lteAccessGroup(function (NavGroup $group) {

                $group->lteRoles();
                $group->ltePermission();
                $group->lteFunctions();
            });
        });

        return $this;
    }

    /**
     * Make default administration group
     * @param  \Closure  $closure
     * @return \Lar\LteAdmin\Core\NavGroup
     */
    public function lteAdministrationGroup(\Closure $closure)
    {
        return $this->group('lte.administration', 'admin', function (NavGroup $group) use ($closure) {

            $closure($group);

        })->icon_cogs();
    }

    /**
     * Make administrator list tool
     * @param  string|null  $action
     * @return \Lar\LteAdmin\Core\NavItem
     */
    public function lteAdministrators(string $action = null)
    {
        return $this->item('lte.administrators', 'administrators')
            ->resource('lte_user', $action ?? '\Lar\LteAdmin\Controllers\AdministratorsController')
            ->icon_users_cog();
    }

    /**
     * Make default access group
     * @param  \Closure  $closure
     * @return \Lar\LteAdmin\Core\NavGroup
     */
    public function lteAccessGroup(\Closure $closure)
    {
        return $this->group('lte.access', 'access', function (NavGroup $group) use ($closure) {

            $closure($group);

        })->icon_universal_access();
    }

    /**
     * Make roles list tool
     * @param  string|null  $action
     * @return \Lar\LteAdmin\Core\NavItem
     */
    public function lteRoles(string $action = null)
    {
        return $this->item('lte.roles', 'roles')
            ->resource('lte_role', $action ?? '\Lar\LteAdmin\Controllers\RolesController')
            ->icon_user_secret();
    }

    /**
     * Make permissions list tool
     * @param  string|null  $action
     * @return \Lar\LteAdmin\Core\NavItem
     */
    public function ltePermission(string $action = null)
    {
        return $this->item('lte.permission', 'permission')
            ->resource('lte_permission', $action ?? '\Lar\LteAdmin\Controllers\PermissionController')
            ->icon_ban();
    }

    /**
     * Make functions/gates list tool
     * @param  string|null  $action
     * @return \Lar\LteAdmin\Core\NavItem
     */
    public function lteFunctions(string $action = null)
    {
        return $this->item('lte.functions', 'functions')
            ->resource('lte_functions', $action ?? '\Lar\LteAdmin\Controllers\FunctionsController')
            ->icon_dungeon()->role('root');
    }
}