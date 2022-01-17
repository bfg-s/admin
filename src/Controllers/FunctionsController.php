<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteRole;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class FunctionsController extends Controller
{
    /**
     * @var string
     */
    static $model = LteFunction::class;

    /**
     * @var array
     */
    static $roles = ['root'];

    public function explanation(): Explanation
    {
        return Explanation::new(
            $this->card()->defaultTools()
        )->index(
            $this->search()->id(),
            $this->search()->input('slug', 'lte.slug'),
            $this->search()->input('class', 'Class', '%=%'),
            $this->search()->updated_at(),
            $this->search()->created_at(),
        )->index(
            $this->table()->id(),
            $this->table()->col('lte.role', [$this, 'show_roles']),
            $this->table()->col('lte.slug', 'slug')->sort()->input_editable()
                ->copied()->to_prepend_link('fas fa-glasses', null, '{class}'),
            $this->table()->col('lte.description', 'description')->to_lang()
                ->has_lang()->str_limit(50)->textarea_editable()->sort(),
            $this->table()->active_switcher(),
            $this->table()->at(),
        )->edit(
            $this->form()->info_id(),
        )->form(
            $this->form()->input('slug', 'lte.slug')->required()
                ->slugable(),
            $this->form()->checks('roles', 'lte.roles')->required()
                ->options(LteRole::all()->pluck('name', 'id')),
            $this->form()->textarea('description', 'lte.description'),
            $this->form()->switcher('active', 'lte.active')->boolean(),
        )->edit(
            $this->form()->info_at(),
        )->show(
            $this->info()->id(),
            $this->info()->row('lte.role', [$this, 'show_roles']),
            $this->info()->row('lte.slug', 'slug')->copied(),
            $this->info()->row('lte.description', 'description')->to_lang()->has_lang()->str_limit(50),
            $this->info()->row('lte.active', 'active')->input_switcher(),
            $this->info()->at(),
        );
    }

    /**
     * @param  LteFunction  $function
     * @return string
     */
    public function show_roles(LteFunction $function)
    {
        return '<span class="badge badge-success">' . $function->roles->pluck('name')->implode('</span> <span class="badge badge-success">') . '</span>';
    }
}
