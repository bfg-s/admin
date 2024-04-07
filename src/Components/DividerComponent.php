<?php

declare(strict_types=1);

namespace Admin\Components;

class DividerComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'divider';

    /**
     * @param  string|callable|null  $right_title
     * @param  string|callable|null  $center_title
     * @param  string|callable|null  $left_title
     * @param ...$explanations
     */
    public function __construct(
        public mixed $right_title = null,
        public mixed $center_title = null,
        public mixed $left_title = null,
        ...$explanations
    ) {
        if (is_callable($this->right_title)) {
            $this->right_title = call_user_func($this->right_title, $this);
        }
        if (is_callable($this->center_title)) {
            $this->center_title = call_user_func($this->center_title, $this);
        }
        if (is_callable($this->left_title)) {
            $this->left_title = call_user_func($this->left_title, $this);
        }

        parent::__construct(...$explanations);
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'right_title' => $this->right_title,
            'center_title' => $this->center_title,
            'left_title' => $this->left_title,
            'anyTitle' => $this->left_title || $this->center_title || $this->right_title,
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
