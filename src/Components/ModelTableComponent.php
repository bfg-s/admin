<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Contents\CardContent;
use Lar\LteAdmin\Components\Traits\ModelTable\TableBuilderTrait;
use Lar\LteAdmin\Components\Traits\ModelTable\TableControlsTrait;
use Lar\LteAdmin\Components\Traits\ModelTable\TableExtensionTrait;
use Lar\LteAdmin\Components\Traits\ModelTable\TableHelpersTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Interfaces\ControllerContainerInterface;
use Lar\LteAdmin\Page;

/**
 * @methods static::$extensions (...$params)
 * @mixin ModelTableComponentMacroList
 * @mixin ModelTableComponentMethods
 */
class ModelTableComponent extends Component implements ControllerContainerInterface
{
    use TableHelpersTrait,
        TableExtensionTrait,
        TableBuilderTrait,
        TableControlsTrait,
        Macroable,
        Piplineble,
        Delegable;

    /**
     * @var string
     */
    protected $element = 'table';
    protected $label = null;
    protected $hasHidden = false;

    /**
     * @var string[]
     */
    protected $props = [
        'table', 'table-sm', 'table-hover',
    ];

    /**
     * @var Model|Builder|Relation|Collection|array|null
     */
    protected $model;

    /**
     * @var LengthAwarePaginator
     */
    protected $paginate;

    /**
     * @var \Closure|array|null
     */
    protected $model_control = [];

    /**
     * @var string
     */
    protected $model_name;

    /**
     * @var string
     */
    protected $model_class;

    /**
     * @var int
     */
    protected $per_page = 15;

    /**
     * @var int[]
     */
    protected $per_pages = [10, 15, 20, 50, 100];

    /**
     * @var string
     */
    protected $order_field = 'id';

    /**
     * @var string
     */
    protected $order_type = 'desc';

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var string|null
     */
    protected $last;

    /**
     * @var bool
     */
    protected $prepend = false;

    /**
     * @var SearchFormComponent
     */
    public $search;

    /**
     * @var Page
     */
    public $page;

    /**
     * @param  array  $delegates
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(array $delegates = [])
    {
        parent::__construct();

        $this->page = app(Page::class);

        $this->model = $this->page->model();

        $this->model_name = $this->getModelName();

        if (request()->has($this->model_name)) {
            $this->order_field = request()->get($this->model_name);
        }

        if (request()->has($this->model_name.'_type')) {
            $type = request()->get($this->model_name.'_type');
            $this->order_type = $type === 'asc' || $type === 'desc' ? $type : 'asc';
        }

        $this->explainForce(Explanation::new($delegates));

        $this->callConstructEvents();

        $this->toExecute('_create_controls', '_build');

        $this->save_table_requests();
    }

    public static function toContainer(DIV $div, $arguments)
    {
        if ($div instanceof CardContent) {
            return $div->bodyModelTable(...$arguments);
        }

        return $div;
    }

    /**
     * Save last table request for returns.
     */
    protected function save_table_requests()
    {
        $all = request()->query();
        unset($all['_pjax']);
        session(['temp_lte_table_data' => $all]);
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|\Lar\Tagable\Tag|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (static::hasExtension($name) && isset($this->columns[$this->last])) {
            $this->columns[$this->last]['macros'][] = [$name, $arguments];

            return $this;
        }

        return parent::__call($name, $arguments);
    }

    public static function registrationInToContainer(Page $page, array $delegates = [])
    {
        if ($page->getContent() instanceof CardContent) {
            $page->registerClass(
                $page->getClass(CardContent::class)->bodyModelTable($delegates)
            );

            $page->getClass(CardContent::class)->headerObj(function (DIV $div) use ($page) {
                $ad = $page->getClass(self::class)->getActionData();
                if ($ad['show']) {
                    $div->prepEnd()->view('lte::segment.model_table_actions', $ad);
                }
            });
        } else {
            $page->registerClass(
                $page->getContent()->model_table($delegates)
            );
        }
    }
}
