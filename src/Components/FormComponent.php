<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\ComponentTabsTrait;
use Illuminate\Support\Facades\Route;

/**
 * Admin panel form component.
 */
class FormComponent extends Component
{
    use ComponentTabsTrait {
        ComponentTabsTrait::tab as helpTab;
    }

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
     * FormComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
    }

    /**
     * Redefining tabs from the helper.
     *
     * @param ...$delegates
     * @return TabsComponent
     */
    public function tab(...$delegates): TabsComponent
    {
        array_unshift($delegates, TabContentComponent::new()->vertical()->p2());

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
            $this->view('components.inputs.hidden', [
                'name' => $name,
                'value' => $value
            ]);
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
            'method' => $this->method,
            'action' => $this->action,
            'onSubmit' => $this->onSubmit,
            'id' => static::$last_id,
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

        static::$last_id = uniqid();

        if ($menu && $menu->getResourceRoute()) {
            $this->hiddens([
                '_after' => session('_after', 'index')
            ]);
        }
    }
}
