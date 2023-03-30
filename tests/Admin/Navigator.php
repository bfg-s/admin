<?php

namespace Admin\Tests\Admin;

use Admin\Core\NavGroup;
use Admin\Core\NavigatorExtensionProvider;
use Admin\Interfaces\ActionWorkExtensionInterface;
use Admin\Tests\Admin\Controllers\UsersController;

/**
 * Navigator Class.
 * @package Admin\Tests\Admin
 */
class Navigator extends NavigatorExtensionProvider implements ActionWorkExtensionInterface
{
    /**
     * @return void
     */
    public function handle(): void
    {
        $this->item('Users')
            ->resource('users', UsersController::class)
            ->icon_users();

        $this->lteAdministrationGroup(static function (NavGroup $group) {
            $group->lteAdministrators();

            $group->lteRoles();

            $group->ltePermission();
        });
    }
}
