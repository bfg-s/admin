<?php

namespace Lar\LteAdmin\Controllers\Administrators;

use Lar\LteAdmin\Controllers\AdministratorsController;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\TabContent;

/**
 * Class MatrixController
 * @package Lar\LteAdmin\Controllers\Administrators
 */
class MatrixController extends AdministratorsController
{
    /**
     * @return Matrix
     */
    public function matrix()
    {
        return new Matrix(['lte.add_admin', 'lte.edit_admin'], function (Form $form, Card $card) {

            $card->defaultTools(function ($type) {
                return $type === 'delete' && $this->model()->id == 1 && admin()->id == $this->model()->id ? false : true;
            });

            $form->info_id();

            $form->image('avatar', 'lte.avatar')->nullable();

            $form->tab('lte.common', 'fas fa-cogs', function (TabContent $tab)  {

                $tab->input('login', 'lte.login_name')
                    ->required()
                    ->unique(LteUser::class, 'login', $this->model()->id);

                $tab->input('name', 'lte.name')->required();

                $tab->email('email', 'lte.email_address')
                    ->required()->unique(LteUser::class, 'email', $this->model()->id);

                $tab->multi_select('roles[]', 'lte.role')->icon_user_secret()
                    ->options(LteRole::all()->pluck('name','id'));
            });

            $form->tab('lte.password', 'fas fa-key', function (TabContent $tab)  {

                $tab->password('password', 'lte.new_password')
                    ->confirm()->required_condition($this->isType('create'));
            });

            $form->info_at();
        });
    }
}