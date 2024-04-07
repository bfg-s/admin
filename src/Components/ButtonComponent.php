<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\FontAwesome;
use Admin\Traits\TypesTrait;

class ButtonComponent extends Component
{
    use TypesTrait;
    use FontAwesome;

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var null|string
     */
    protected ?string $title = null;

    /**
     * @var string
     */
    protected string $view = 'button';

    /**
     * @var string
     */
    protected string $typeAttribute = 'button';

    /**
     * @return void
     */
    protected function mount(): void
    {

    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'typeAttribute' => $this->typeAttribute,
            'type' => $this->type,
            'icon' => $this->icon,
            'title' => $this->title,
        ];
    }

    /**
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->typeAttribute = $type;

        return $this;
    }

    /**
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
     * @param  array  $unset
     * @param  array  $params
     * @return $this
     */
    public function unsetQuery(array $unset = [], array $params = []): static
    {
        return $this->query($params, $unset);
    }

    /**
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
     * @param  string|array  $name
     * @return $this
     */
    public function forgetQuery(string|array $name): static
    {
        $this->query([], (array) $name);

        return $this;
    }

    /**
     * @param  array  $data
     * @return $this
     */
    public function iconTitle(array $data): static
    {
        $this->icon($data[0] ?? '');
        $this->title($data[1] ?? '');

        return $this;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * @param  string  $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
