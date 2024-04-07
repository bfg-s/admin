<?php

declare(strict_types=1);

namespace Admin\Core;

use Illuminate\Contracts\Support\Arrayable;
use Admin\Components\Component;
use Admin\Traits\FontAwesome;

class ModelTableAction implements Arrayable
{
    use FontAwesome;

    protected $title = 'Action';
    protected $icon = 'fas fa-dot-circle';
    protected $confirm = null;
    protected $warning = 'admin.before_need_to_select';

    public function __construct(
        protected $model,
        protected $callback,
        protected $callback_parameters = [],
    ) {
    }

    public function toArray()
    {
        if (
            $this->title
            && $this->callback
            && $jax = Component::registerCallBack($this->callback, $this->callback_parameters, $this->model)
        ) {
            return [
                'jax' => json_encode($jax),
                'title' => $this->title,
                'icon' => $this->icon,
                'confirm' => $this->confirm,
                'warning' => $this->warning,
            ];
        }
        return [];
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

    /**
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  string  $confirmMessage
     * @return $this
     */
    public function confirm(string $confirmMessage): static
    {
        $this->confirm = $confirmMessage;

        return $this;
    }

    /**
     * @param  string  $warningMessage
     * @return $this
     */
    public function warning(string $warningMessage): static
    {
        $this->warning = $warningMessage;

        return $this;
    }

    /**
     * @return static
     */
    public function nullable(): static
    {
        $this->warning = null;

        return $this;
    }
}
