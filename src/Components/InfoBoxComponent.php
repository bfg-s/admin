<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\Delegable;
use Admin\Traits\FontAwesome;
use Admin\Traits\TypesTrait;

class InfoBoxComponent extends Component
{
    use FontAwesome;
    use TypesTrait;
    use Delegable;

    /**
     * @var string
     */
    protected string $view = 'info-box';

    /**
     * @var string|null
     */
    private ?string $title = null;

    /**
     * @var string|null
     */
    private ?string $icon = null;

    /**
     * @var string|array|null
     */
    private string|array|null $link = null;

    /**
     * @var string|mixed
     */
    private mixed $body = null;

    /**
     * @var array
     */
    private array $params = [];

    /**
     * @param  string|null  $title
     * @param  string  $body
     * @param  string  $icon
     * @param ...$params
     */
    public function __construct(string $title = null, string $body = '', string $icon = 'fas fa-info-circle', ...$params)
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
     * @param  string  $link
     * @param  string|null  $text
     * @param  string|null  $icon
     * @return $this
     */
    public function link(string $link, string $text = null, string $icon = null): static
    {
        $this->link = [$link, $text, $icon];

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
            'body' => is_array($this->body) ? $this->body : [$this->body],
            'title' => $this->title,
            'icon' => $this->icon,
            'link' => $this->link ? (!is_array($this->link) ? [$this->link] : $this->link) : null,
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
