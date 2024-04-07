<?php

declare(strict_types=1);

namespace Admin\Components;

use Closure;
use Admin\Traits\FontAwesome;
use Admin\Traits\TypesTrait;

class AlertComponent extends Component
{
    use FontAwesome;
    use TypesTrait;

    /**
     * @var string
     */
    protected string $view = 'alert';

    /**
     * @var string|null
     */
    protected ?string $title;

    /**
     * @var string|null
     */
    protected ?string $icon;

    /**
     * @var string|mixed
     */
    protected mixed $body;

    /**
     * @var array
     */
    protected array $params;

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
     * @param  string|array|Closure  $body
     * @return $this
     */
    public function body($body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'title' => __($this->title),
            'icon' => $this->icon,
            'type' => $this->type,
            'body' => is_string($this->body) ? __($this->body) : $this->body
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {

    }
}
