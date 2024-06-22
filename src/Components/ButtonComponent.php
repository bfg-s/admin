<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\FontAwesomeTrait;
use Admin\Traits\Typeable;

/**
 * The button component is responsible for all buttons in the admin panel.
 */
class ButtonComponent extends Component
{
    use Typeable;
    use FontAwesomeTrait;

    /**
     * Name of the button icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * The text indicated on the button.
     *
     * @var null|string
     */
    protected ?string $title = null;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'button';

    /**
     * Button type attribute.
     *
     * @var string
     */
    protected string $typeAttribute = 'button';

    /**
     * Display the button as a none.
     *
     * @var bool
     */
    protected bool $displayNone = false;

    /**
     * Set an icon to the button.
     *
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * Set the text that will be displayed on the button.
     *
     * @param  string  $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the button type attribute.
     *
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->typeAttribute = $type;

        return $this;
    }

    /**
     * By clicking, call up the modal window specified by name.
     *
     * @param  string  $modalName
     * @param  array  $query
     * @return $this
     */
    public function modal(string $modalName = "modal", array $query = []): static
    {
        $this->on_click([
            'modal:put' => [
                $modalName,
                $query,
            ],
        ]);

        return $this;
    }

    /**
     * Destroy the currently called modal window.
     *
     * @return $this
     */
    public function modalDestroy(): static
    {
        if (request()->_modal_id) {
            $this->on_click('modal:destroy', request()->_modal_id);
        }

        return $this;
    }

    /**
     * Hide the currently called modal window.
     *
     * @return $this
     */
    public function modalHide(): static
    {
        if (request()->_modal_id) {
            $this->on_click('modal:hide', request()->_modal_id);
        }

        return $this;
    }

    /**
     * Submit the form from the currently called modal window.
     *
     * @return $this
     */
    public function modalSubmit(string $after = "destroy"): static
    {
        if (request()->_modal_id) {
            $this->on_click('modal:submit', [request()->_modal_id, $after]);
        }

        return $this;
    }

    /**
     * Go to the page and add or reset the query parameter "method".
     *
     * @param  string|null  $method
     * @return $this
     */
    public function queryMethod(string $method = null): static
    {
        if ($method) {
            $this->query(['method' => $method]);
        } else {
            $this->query([], ['method']);
        }

        return $this;
    }

    /**
     * Go to the page and add or unset any query parameter.
     *
     * @param  array  $params
     * @param  array  $unset
     * @return $this
     */
    public function query(array $params = [], array $unset = []): static
    {
        $this->on_click('location', admin_url_with_get($params, $unset));

        return $this;
    }

    /**
     * Go to the page and unset or add any query parameter.
     *
     * @param  array  $unset
     * @param  array  $params
     * @return $this
     */
    public function unsetQuery(array $unset = [], array $params = []): static
    {
        return $this->query($params, $unset);
    }

    /**
     * Go to the page and switch any query parameter, if there is one, remove it, if not, add it.
     *
     * @param  string|array  $name
     * @param  mixed  $value
     * @return $this
     */
    public function switchQuery(string|array $name, mixed $value = 1): static
    {
        if (request()->has($name)) {
            $this->query([], (array) $name);
        } else {
            $this->query(array_fill_keys((array) $name, $value));
        }

        return $this;
    }

    /**
     * The wrapper over `query` accepts multiple keys.
     *
     * @param  string|array  $name
     * @param  mixed  $value
     * @return $this
     */
    public function setQuery(string|array $name, mixed $value = 1): static
    {
        $this->query(array_fill_keys((array) $name, $value));

        return $this;
    }

    /**
     * Remove the query parameter or parameters.
     *
     * @param  string|array  $name
     * @return $this
     */
    public function forgetQuery(string|array $name): static
    {
        $this->query([], (array) $name);

        return $this;
    }

    /**
     * Set the button to be displayed as none.
     *
     * @return $this
     */
    public function displayNone(): static
    {
        $this->displayNone = true;

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
            'typeAttribute' => $this->typeAttribute,
            'type' => $this->type,
            'icon' => $this->icon,
            'title' => __($this->title),
            'displayNone' => $this->displayNone,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
    }
}
