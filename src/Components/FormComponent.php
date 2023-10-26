<?php

namespace Admin\Components;

use Admin\Traits\BuildHelperTrait;
use Illuminate\Support\Facades\Route;

class FormComponent extends Component
{
    use BuildHelperTrait {
        BuildHelperTrait::tab as helpTab;
    }

    /**
     * @var string|null
     */
    public static ?string $last_id = null;

    /**
     * @var string
     */
    protected string $view = 'form';

    /**
     * @var string
     */
    protected string $method = 'post';

    /**
     * @var string|null
     */
    protected ?string $action = null;

    /**
     * @var string
     */
    protected string $onSubmit = '';

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
    }

    /**
     * @param ...$delegates
     * @return TabsComponent
     */
    public function tab(...$delegates): TabsComponent
    {
        array_unshift($delegates, TabContentComponent::new()->vertical()->p2());

        return $this->helpTab(...$delegates);
    }

    /**
     * @param  string  $method
     * @return $this
     */
    public function method(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param  string  $action
     * @return $this
     */
    public function action(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param  string  $onSubmit
     * @return void
     */
    public function setOnSubmit(string $onSubmit): void
    {
        $this->onSubmit = $onSubmit;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        $menu = $this->menu;

        $type = $this->page->resource_type;

        if (!$this->action && $type && $this->model && $menu) {
            $key = $this->model->getOriginal($this->model->getRouteKeyName());

            if ($type === 'edit' && $menu->isResource()) {
                $this->action = $menu->getLinkUpdate($key);
                $this->hiddens(['_method' => 'PUT']);
            } elseif ($type === 'create' && $menu->isResource()) {
                $this->action = $menu->getLinkStore();
            }
        } elseif ($menu && $menu->getPost() && $menu->getRoute() && Route::has($menu->getRoute().'.post')) {
            $this->action = route($menu->getRoute().'.post', $menu->getRouteParams() ?? []);
        }

        if (!$this->action) {
            $this->action = url()->current();
        }

        static::$last_id = uniqid();

        if ($menu && $menu->getResourceRoute()) {
            $this->hiddens([
                '_after' => session('_after', 'index')
            ]);
        }
    }

    /**
     * @param  array  $fields
     * @return $this
     */
    public function hiddens(array $fields): static
    {
        foreach ($fields as $name => $value) {

            $this->view('components.inputs.hidden', [
                'name' => $name,
                'value' => $value
            ]);
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'method' => $this->method,
            'action' => $this->action,
            'onSubmit' => $this->onSubmit,
            'id' => static::$last_id,
        ];
    }
}
