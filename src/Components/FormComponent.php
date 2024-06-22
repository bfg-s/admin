<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Facades\Admin;
use Admin\Traits\ComponentTabsTrait;
use Admin\Traits\Resouceable;
use Illuminate\Support\Facades\Route;

/**
 * Admin panel form component.
 */
class FormComponent extends Component
{
    use ComponentTabsTrait {
        ComponentTabsTrait::tab as helpTab;
    }
    use Resouceable;

    /**
     * Last form ID.
     *
     * @var string|null
     */
    public static ?string $last_id = null;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'form';

    /**
     * Method for submitting form data.
     *
     * @var string
     */
    protected string $method = 'post';

    /**
     * Action property where to send form data.
     *
     * @var string|null
     */
    protected ?string $action = null;

    /**
     * Event attribute for submitting form data.
     *
     * @var string
     */
    protected string $onSubmit = '';

    /**
     * Save form mode for the component.
     *
     * @var bool
     */
    protected bool $saveForm = true;

    /**
     * FormComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->delegatesNow(...$delegates);
    }

    /**
     * Redefining tabs from the helper.
     *
     * @param ...$delegates
     * @return TabsComponent
     */
    public function tab(...$delegates): TabsComponent
    {
        array_unshift($delegates, TabContentComponent::new()->vertical()->padding(2));

        return $this->helpTab(...$delegates);
    }

    /**
     * Set the method for submitting form data.
     *
     * @param  string  $method
     * @return $this
     */
    public function method(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set the action to submit form data.
     *
     * @param  string  $action
     * @return $this
     */
    public function action(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set the value of the form submit event attribute.
     *
     * @param  string  $onSubmit
     * @return $this
     */
    public function setOnSubmit(string $onSubmit): static
    {
        $this->onSubmit = $onSubmit;

        return $this;
    }

    /**
     * Add a list of hidden form inputs.
     *
     * @param  array  $fields
     * @return $this
     */
    public function hiddens(array $fields): static
    {
        foreach ($fields as $name => $value) {

            $this->hidden($name)->value($value);
        }

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'id' => static::$last_id,
            'method' => $this->method,
            'action' => $this->action,
            'onSubmit' => $this->onSubmit,
        ];
    }

    /**
     * Additional data to be sent to the API.
     *
     * @return array
     */
    protected function apiData(): array
    {
        if ($this->model && $this->model->exists) {

            Admin::important('model', $this->model, $this->getResource());
        }

        return [
            'id' => static::$last_id,
            'method' => $this->method,
            'action' => $this->action,
            'onSubmit' => $this->onSubmit,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
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

        static::$last_id = $this->action ? md5($this->action) : uniqid();

        if ($menu && $menu?->getResourceRoute()) {
            $this->hiddens([
                '_after' => session('_after', 'index')
            ]);
        }

        $this->dataLoad('valid');

        if ($this->saveForm) {

            $this->dataLoad('save::form');
        }
    }
}
