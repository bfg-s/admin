<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Components\Component;
use Admin\Traits\FontAwesomeTrait;
use Illuminate\Contracts\Support\Arrayable;

/**
 * The part of the kernel that is responsible for the action of the model table or model cards.
 */
class ModelTableAction implements Arrayable
{
    use FontAwesomeTrait;

    /**
     * Action title.
     *
     * @var string
     */
    protected string $title = 'Action';

    /**
     * Action icon.
     *
     * @var string
     */
    protected string $icon = 'fas fa-dot-circle';

    /**
     * Confirmation for action before action.
     *
     * @var string|null
     */
    protected string|null $confirm = null;

    /**
     * Warning for action before action without selected rows.
     *
     * @var string|null
     */
    protected string|null $warning = 'admin.before_need_to_select';

    /**
     * ModelTableAction constructor.
     *
     * @param $model
     * @param $callback
     * @param  array  $callback_parameters
     */
    public function __construct(
        protected $model,
        protected $callback,
        protected array $callback_parameters = [],
    ) {
    }

    /**
     * Convert the action class to an array.
     *
     * @return array
     */
    public function toArray(): array
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
     * Set the action title.
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
     * Set action icon.
     *
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set action confirmation.
     *
     * @param  string  $confirmMessage
     * @return $this
     */
    public function confirm(string $confirmMessage): static
    {
        $this->confirm = $confirmMessage;

        return $this;
    }

    /**
     * Set action warning.
     *
     * @param  string  $warningMessage
     * @return $this
     */
    public function warning(string $warningMessage): static
    {
        $this->warning = $warningMessage;

        return $this;
    }

    /**
     * Remove action warning.
     *
     * @return static
     */
    public function nullable(): static
    {
        $this->warning = null;

        return $this;
    }
}
