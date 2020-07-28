<?php

namespace Lar\LteAdmin\Controllers;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\Container;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\SearchForm;
use Lar\LteAdmin\Segments\Tagable\TabContent;
use Lar\LteAdmin\Segments\Tagable\Tabs;

/**
 * Class AdministratorsController
 * @package Lar\LteAdmin\Controllers
 */
class AdministratorsController extends Controller
{
    /**
     * @var string
     */
    static $model = LteUser::class;

    /**
     * @param  LteUser  $user
     * @return string
     */
    public function show_role(LteUser $user)
    {
        return '<span class="badge badge-success">' . $user->roles->pluck('name')->implode('</span> <span class="badge badge-success">') . '</span>';
    }
}
