<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Model;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\SPAN;
use Lar\LteAdmin\Components\Contents\CardContent;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Interfaces\ControllerContainerInterface;
use Lar\LteAdmin\Page;

/**
 * @methods Lar\LteAdmin\Components\ModelTableComponent::$extensions (...$params)
 * @mixin ModelInfoTableComponentMacroList
 * @mixin ModelInfoTableComponentMethods
 */
class ModelInfoTableComponent extends DIV implements ControllerContainerInterface
{
    use Macroable, Piplineble, Delegable;

    /**
     * @var bool
     */
    protected $only_content = true;

    /**
     * @var array|Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var string|null
     */
    protected $last;

    /**
     * @var Page
     */
    public $page;

    /**
     * @param  array  $delegates
     */
    public function __construct(array $delegates = [])
    {
        $this->page = app(Page::class);

        $this->model = $this->page->model();

        parent::__construct();

        $this->explainForce(Explanation::new($delegates));

        $this->toExecute('buildTable');

        $this->callConstructEvents();
    }

    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  string|\Closure|array  $field
     * @param  string  $label
     * @return $this
     */
    public function row(string $label, $field)
    {
        $this->last = uniqid('row');

        $this->rows[$this->last] = ['field' => $field, 'label' => $label, 'info' => false, 'macros' => []];

        return $this;
    }

    /**
     * @param  string  $info
     * @return $this
     */
    public function info(string $info)
    {
        if ($this->last && isset($this->rows[$this->last])) {
            $this->rows[$this->last]['info'] = $info;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function id()
    {
        $this->row('lte.id', 'id');

        return $this;
    }

    /**
     * @return $this
     */
    public function created_at()
    {
        $this->row('lte.created_at', 'created_at')->true_data();

        return $this;
    }

    /**
     * @return $this
     */
    public function updated_at()
    {
        $this->row('lte.updated_at', 'updated_at')->true_data();

        return $this;
    }

    /**
     * @return $this
     */
    public function deleted_at()
    {
        $this->row('lte.deleted_at', 'deleted_at')->true_data();

        return $this;
    }

    /**
     * @return $this
     */
    public function at()
    {
        $this->updated_at()->created_at();

        return $this;
    }

    /**
     * @return $this
     */
    public function active_switcher()
    {
        $this->row('lte.active', 'active')->yes_no();

        return $this;
    }

    /**
     * Build table.
     */
    protected function buildTable()
    {
        $this->callRenderEvents();

        $data = [];

        if ($this->model) {
            foreach ($this->rows as $row) {
                $field = $row['field'];
                $label = $row['label'];
                $macros = $row['macros'];
                if (is_string($field)) {
                    $field = e(multi_dot_call($this->model, $field));
                } elseif (is_array($field) || is_embedded_call($field)) {
                    $field = embedded_call($field, [
                        is_object($this->model) ? get_class($this->model) : 'model' => $this->model,
                        self::class => $this,
                    ]);
                }
                foreach ($macros as $macro) {
                    $field = ModelTableComponent::callExtension($macro[0], [
                        'model' => ! is_array($this->model) ? $this->model : null,
                        'value' => $field,
                        'field' => $row['field'],
                        'title' => $row['label'],
                        'props' => $macro[1],
                        (is_object($this->model) ? get_class($this->model) : gettype($this->model)) => $this->model,
                    ]);
                }
                $label = __($label);
                if ($row['info']) {
                    $label .= SPAN::create(':space')->i(['title' => __($row['info'])])->icon_info_circle()->_render();
                }
                $data[] = [$label, $field];
            }
        }

        $this->table()->rows($data);
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|bool|ModelInfoTableComponent|\Lar\Tagable\Tag|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (ModelTableComponent::hasExtension($name) && $this->last) {
            $this->rows[$this->last]['macros'][] = [$name, $arguments];

            return $this;
        }

        return parent::__call($name, $arguments);
    }

    public static function registrationInToContainer(Page $page, array $delegates = [])
    {
        if ($page->getContent() instanceof CardContent) {
            $page->registerClass(
                $page->getClass(CardContent::class)->fullBody()->model_info_table($delegates)
            );
        } else {
            $page->registerClass(
                $page->getContent()->model_info_table($delegates)
            );
        }
    }
}
