<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\INPUT;
use Lar\LteAdmin\Components\Contents\CardContent;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Components\Traits\FormAutoMakeTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Interfaces\ControllerContainerInterface;
use Lar\LteAdmin\Page;

/**
 * @macro_return Lar\LteAdmin\Components\FormGroup
 * @methods Lar\LteAdmin\Components\FieldComponent::$form_components (string $name, string $label = null, ...$params)
 * @mixin FormComponentMethods
 * @mixin FormComponentMacroList
 */
class FormComponent extends \Lar\Layout\Tags\FORM implements ControllerContainerInterface
{
    use FieldMassControlTrait,
        FormAutoMakeTrait,
        Macroable,
        Piplineble,
        BuildHelperTrait,
        Delegable;

    /**
     * @var Model|null
     */
    public static $current_model;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $method = 'post';

    /**
     * @var string|null
     */
    protected $action;

    /**
     * @var string
     */
    public static $last_id;

    /**
     * @var Page
     */
    public $page;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->page = app(Page::class);

        $this->model($this->page->model());

        parent::__construct();

        $this->explainForce(Explanation::new($delegates));

        $this->toExecute('buildForm');

        $this->callConstructEvents();
    }

    /**
     * @param  $model
     * @return $this
     */
    public function model($model)
    {
        $this->model = $model;

        static::$current_model = $this->model;

        return $this;
    }

    /**
     * @param  string  $method
     * @return $this
     */
    public function method(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param  string  $action
     * @return $this
     */
    public function action(string $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Form builder.
     */
    protected function buildForm()
    {
        $this->callRenderEvents();

        $this->setMethod($this->method);

        $menu = gets()->lte->menu->now;

        $type = gets()->lte->menu->type;

        if (isset($menu['model.param'])) {
            $this->appEnd(
                INPUT::create(['type' => 'hidden', 'name' => '_after', 'value' => session('_after', 'index')])
            );
        }

        if (! $this->action && $type && $this->model && $menu) {
            $key = $this->model->getOriginal($this->model->getRouteKeyName());

            if ($type === 'edit' && isset($menu['link.update'])) {
                $this->action = $menu['link.update']($key);
                $this->hiddens(['_method' => 'PUT']);
            } elseif ($type === 'create' && isset($menu['link.store'])) {
                $this->action = $menu['link.store']();
            }
        } elseif (isset($menu['post']) && isset($menu['route']) && \Route::has($menu['route'].'.post')) {
            $this->action = route($menu['route'].'.post', $menu['route_params'] ?? []);
        }

        if (! $this->action) {
            $this->action = url()->current();
        }

        $this->setAction($this->action);

        $this->setEnctype('multipart/form-data');

        static::$last_id = $this->getUnique();

        $this->setId(static::$last_id);

        $this->attr('data-load', 'valid');

        static::$current_model = null;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormComponent|\Lar\Tagable\Tag|mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ($call = $this->call_group($name, $arguments)) {
            $call->setModel($this->model);

            return $call;
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @param Model|Builder|string $model
     * @param  \Closure  $closure
     */
    public static function withModel($model, \Closure $closure)
    {
        $tmp_model = static::$current_model;
        static::$current_model = $model;
        $closure();
        static::$current_model = $tmp_model;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FieldComponent|FormGroupComponent|mixed
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        if ($call = static::static_call_group($name, $arguments)) {
            if (static::$current_model) {
                $call->setModel(static::$current_model);
            }

            if (Component::$last_component) {
                Component::$last_component->appEnd($call);
            }

            return $call;
        }

        return parent::__callStatic($name, $arguments);
    }

    public static function registrationInToContainer(Page $page, array $delegates = [])
    {
        if ($page->getContent() instanceof CardContent) {
            $card = $page->getClass(CardContent::class);
            $page->registerClass($card->bodyForm($delegates));
            $page->registerClass($card->footerForm());
        } else {
            $page->registerClass($page->getContent()->form($delegates));
        }
    }
}
