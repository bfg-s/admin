<?php

namespace LteAdmin\Components;

use Lar\Layout\Traits\FontAwesome;
use LteAdmin\Traits\TypesTrait;

class ButtonComponent extends Component
{
    use TypesTrait, FontAwesome;

    protected $icon;
    protected $title;

    protected $class = 'btn btn-xs';

    /**
     * Tag element.
     *
     * @var string
     */
    protected $element = 'button';

    /**
     * @param  string  $modalName
     * @param  array  $query
     * @return $this
     */
    public function modal(string $modalName = "modal", array $query = []): static
    {
        $this->on_click(json_encode([
            'modal:put' => [
                $modalName,
                $query,
            ],
        ]));

        return $this;
    }

    /**
     * @return $this
     */
    public function modalDestroy()
    {
        if (request()->_modal_id) {
            $this->on_click('modal:destroy', request()->_modal_id);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function modalHide()
    {
        if (request()->_modal_id) {
            $this->on_click('modal:hide', request()->_modal_id);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function modalSubmit(string $after = "destroy")
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
    public function queryMethod(string $method = null)
    {
        if ($method) {
            $this->location(['method' => $method]);
        } else {
            $this->location([], ['method']);
        }

        return $this;
    }

    /**
     * @param  array  $params
     * @param  array  $unset
     * @return $this
     */
    public function location(array $params = [], array $unset = [])
    {
        $this->on_click('doc::location', urlWithGet($params, $unset));

        return $this;
    }

    /**
     * @param  string|array  $name
     * @param  int  $value
     * @return $this
     */
    public function switchQuery(string|array $name, $value = 1)
    {
        if (request()->has($name)) {
            $this->location([], (array) $name);
        } else {
            $this->location(array_fill_keys((array) $name, $value));
        }

        return $this;
    }

    public function setQuery(string|array $name, $value = 1)
    {
        $this->location(array_fill_keys((array) $name, $value));
        return $this;
    }

    public function forgetQuery(string|array $name)
    {
        $this->location([], (array) $name);
        return $this;
    }

    public function iconTitle(array $data)
    {
        $this->icon($data[0] ?? '');
        $this->title($data[1] ?? '');

        return $this;
    }

    public function icon(string $name)
    {
        $this->icon = $name;

        return $this;
    }

    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }

    protected function mount()
    {
        $this->addClass("btn-$this->type");
        if ($this->icon) {
            $this->i([$this->icon])->_text($this->title ? ':space' : '');
        }
        if ($this->title && $this->icon) {
            $this->text("<span class='d-none d-sm-inline'>{$this->title}</span>");
        } else {
            if ($this->title) {
                $this->text($this->title);
            }
        }
    }
}
