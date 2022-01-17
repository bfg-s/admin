<?php

namespace Lar\LteAdmin\Controllers;

use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Segments\LtePage;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class RolesController extends Controller
{
    /**
     * @var string
     */
    static $model = LteRole::class;

    public function explanation(): Explanation
    {
        return Explanation::new(
            $this->card()->defaultTools()
        )->index(
            $this->search()->id(),
            $this->search()->input('name', 'lte.title'),
            $this->search()->input('slug', 'lte.slug'),
            $this->search()->at(),
        )->index(
            $this->table()->id(),
            $this->table()->col('lte.title', 'name')->sort(),
            $this->table()->col('lte.slug', 'slug')->sort()->badge('success'),
            $this->table()->at(),
        )->edit(
            $this->form()->info_id(),
        )->form(
            $this->form()->input('name', 'lte.title')->required()->duplication_how_slug('#input_slug'),
            $this->form()->input('slug', 'lte.slug')->required()->slugable(),
        )->edit(
            $this->form()->info_at(),
        )->show(
            $this->info()->id(),
            $this->info()->row('lte.title', 'name'),
            $this->info()->row('lte.slug', 'slug')->badge('success'),
            $this->info()->at(),
        );
    }

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function index(LtePage $page)
    {
        return $page
            ->card('lte.list_of_roles')
            ->search()
            ->table();
    }

    /**
     * @param  LtePage  $page
     * @return LtePage
     */
    public function matrix(LtePage $page)
    {
        return $page
            ->card(['lte.add_role', 'lte.edit_role'])
            ->form();
    }
}
