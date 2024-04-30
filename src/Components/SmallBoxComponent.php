<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\Delegable;
use Admin\Traits\FontAwesome;
use Admin\Traits\TypesTrait;

class SmallBoxComponent extends Component
{
    use FontAwesome;
    use TypesTrait;
    use Delegable;

    /**
     * @var string
     */
    protected string $view = 'small-box';

    /**
     * @var string|null
     */
    private ?string $title = null;

    /**
     * @var string|null
     */
    private ?string $icon = null;

    /**
     * @var string|mixed
     */
    private mixed $body = null;

    /**
     * @var array
     */
    private array $params = [];

    /**
     * Alert constructor.
     * @param  string|null  $title
     * @param  string  $body
     * @param  string  $icon
     * @param  mixed  ...$params
     */
    public function __construct(string $title = null, mixed $body = '', string $icon = 'fas fa-info-circle', ...$params)
    {
        parent::__construct();

        $this->title = $title;

        $this->icon = $icon;

        $this->body = $body;

        $this->params = $params;
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
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * @param  array|string  $body
     * @param  string  $small_info
     * @return $this
     */
    public function body(array|string $body, string $small_info = ''): static
    {
        $this->body = [$body, $small_info];

        return $this;
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'type' => $this->type,
            'icon' => $this->icon,
            'title' => $this->title,
            'body' => is_array($this->body) ? $this->body : [$this->body],
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
    }
}
