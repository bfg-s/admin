<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\INPUT;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Page;
use Route;

class FormComponent extends Component
{
    use BuildHelperTrait {
        BuildHelperTrait::tab as helpTab;
    }

    /**
     * @var string
     */
    public static $last_id;
    protected $element = 'form';
    /**
     * @var string
     */
    protected $method = 'post';
    /**
     * @var string|null
     */
    protected $action;

    public static function registrationInToContainer(Page $page, array $delegates = [])
    {
        if ($page->getContent() instanceof CardComponent) {
            $card = $page->getClass(CardComponent::class);
            $page->registerClass($card->bodyForm($delegates));
            $page->registerClass($card->footerForm());
        } else {
            $page->registerClass($page->getContent()->form($delegates));
        }
    }

    public function tab(...$delegates)
    {
        array_unshift($delegates, TabContentComponent::new()->vertical()->p2());

        return $this->helpTab(...$delegates);
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

    protected function mount()
    {
        $this->buildForm();
    }

    /**
     * Form builder.
     */
    protected function buildForm()
    {
        $this->callRenderEvents();

        $this->setMethod($this->method);

        $menu = $this->menu;

        $type = $this->page->resource_type;

        if (isset($menu['model.param'])) {
            $this->appEnd(
                INPUT::create(['type' => 'hidden', 'name' => '_after', 'value' => session('_after', 'index')])
            );
        }

        if (!$this->action && $type && $this->model && $menu) {
            $key = $this->model->getOriginal($this->model->getRouteKeyName());

            if ($type === 'edit' && isset($menu['link.update'])) {
                $this->action = $menu['link.update']($key);
                $this->hiddens(['_method' => 'PUT']);
            } elseif ($type === 'create' && isset($menu['link.store'])) {
                $this->action = $menu['link.store']();
            }
        } elseif (isset($menu['post']) && isset($menu['route']) && Route::has($menu['route'].'.post')) {
            $this->action = route($menu['route'].'.post', $menu['route_params'] ?? []);
        }

        if (!$this->action) {
            $this->action = url()->current();
        }

        $this->setAction($this->action);

        $this->setEnctype('multipart/form-data');

        static::$last_id = $this->getUnique();

        $this->setId(static::$last_id);

        $this->attr('data-load', 'valid');
    }
}
