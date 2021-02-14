<?php

namespace Admin\Repositories;

use Admin\Models\AdminPage;
use Bfg\Dev\Support\CoreRepository;

/**
 * Class AdminPageRepository
 * @package Admin\Repositories
 * @property-read AdminPage[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $position_menu_items
 */
class AdminPageRepository extends CoreRepository
{
    /**
     * Model class namespace getter
     *
     * @return string
     */
    protected function getModelClass(): string
    {
        return AdminPage::class;
    }

    /**
     * @return AdminPage[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function position_menu_items()
    {
        return AdminPage::wherePosition(AdminPage::POSITION_MENU)
            ->whereActive(1)
            //->whereNull('parent_id')
            //->with('childs')
            ->orderBy('order')
            ->get();
    }
}
