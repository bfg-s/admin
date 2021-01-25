<?php

namespace Admin\Components\Layout;

use Admin\Http\Resources\AdminMenuResource;
use Admin\Models\AdminPage;
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
     */
    public function __construct()
    {
        $this->items = AdminMenuResource::collection(
            AdminPage::wherePosition(AdminPage::POSITION_MENU)
                ->whereActive(1)
                ->whereNull('parent_id')
                ->with('childs')
                ->orderBy('order')
                ->get()
        );
    }
}