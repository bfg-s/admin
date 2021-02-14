<?php

namespace Admin\Components\Layout;

use Admin\Http\Resources\AdminMenuResource;
use Admin\Models\AdminPage;
use Admin\Repositories\AdminPageRepository;
use Bfg\Layout\View\Component;

/**
 * Class Menu
 * @package Admin\Components\Layout
 */
class Menu extends Component
{
    /**
     * Menu items
     * @var array
     */
    public $items = [];

    /**
     * The component alias name.
     *
     * @var string
     */
    public $componentName = "bfg::layout.menu";

    /**
     * @var string
     */
    protected static $slotable = 'menu';

    /**
     * Menu constructor.
     * @param  AdminPageRepository  $repository
     */
    public function __construct(AdminPageRepository $repository)
    {
        $this->items = $repository
            ->wrap(AdminMenuResource::class)
            ->position_menu_items;
    }
}