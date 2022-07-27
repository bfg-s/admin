<?php

namespace LteAdmin\Traits;

use Closure;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LteAdmin\Controllers\AdministratorsController;
use LteAdmin\Controllers\FunctionsController;
use LteAdmin\Controllers\MenuController;
use LteAdmin\Controllers\PermissionController;
use LteAdmin\Controllers\RolesController;
use LteAdmin\Core\NavGroup;
use LteAdmin\Core\NavItem;
use LteAdmin\LteAdmin;
use LteAdmin\Models\LteMenu;
use LteAdmin\Navigate;

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
            $group->lteMenu();
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
     * Make menu list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function lteMenu(string $action = null)
    {
        return $this->item('lte.admin_menu', 'menu')
            ->resource('lte_menu', $action ?? MenuController::class)
            ->icon_bars();
    }

    public function makeMenu()
    {
        $db = config('lte.connections.lte-sqlite.database');

        if (is_file($db) && Schema::connection('lte-sqlite')->hasTable('lte_menu')) {
            LteMenu::where('active', 1)
                ->orderBy('order')
                ->whereNull('parent_id')
                ->with('child')
                ->get()
                ->map(fn(LteMenu $menu) => $this->injectRemoteMenu($menu));
        }
    }

    protected function injectRemoteMenu(LteMenu $menu, NavGroup $rootGroup = null)
    {
        $rootGroup = $rootGroup ?: $this;

        if ($menu->type === 'item') {
            $rootGroup->item($menu->name, $menu->route, Str::parseCallback($menu->action))
                ->icon($menu->icon);
        } else {
            if ($menu->type === 'resource') {
                $rootGroup->item($menu->name)
                    ->resource($menu->route, $menu->action)
                    ->icon($menu->icon)
                    ->except(...($menu->except ?: []));
            } else {
                $rootGroup->group(
                    $menu->name,
                    $menu->route,
                    fn(NavGroup $group) => $menu->child->map(fn(LteMenu $lteMenu) => $this->injectRemoteMenu($lteMenu,
                        $group))
                )->icon($menu->icon);
            }
        }
    }

    /**
     * @return void
     */
    public function makeExtensions()
    {
        $extensions = LteAdmin::$nav_extensions;

        if (count($extensions) > 1) {
            $this->menu_header('lte.extensions');
        }

        foreach ($extensions as $key => $extension) {
            if ($key === 'application') {
                continue;
            }

            if (is_array($extension)) {
                foreach ($extension as $item) {
                    Navigate::$extension = $item;

                    $item->navigator($this);

                    Navigate::$extension = null;
                }
            } else {
                Navigate::$extension = $extension;

                $extension->navigator($this);

                Navigate::$extension = null;
            }

            unset(LteAdmin::$nav_extensions[$key]);
        }
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
