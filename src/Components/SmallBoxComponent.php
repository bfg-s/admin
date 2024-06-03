<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\FontAwesomeTrait;
use Admin\Traits\Typeable;

/**
 * Small box component of the admin panel.
 */
class SmallBoxComponent extends Component
{
    use FontAwesomeTrait;
    use Typeable;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'small-box';

    /**
     * Small box title.
     *
     * @var string|null
     */
    private string|null $title = null;

    /**
     * Small box icon.
     *
     * @var string|null
     */
    private string|null $icon = null;

    /**
     * Small box body.
     *
     * @var string|mixed
     */
    private mixed $body = null;

    /**
     * Realtime marker, if enabled, the component will be updated at the specified frequency.
     *
     * @var bool
     */
    protected bool $realTime = true;

    /**
     * SmallBoxComponent constructor.
     *
     * @param  string|null  $title
     * @param  string  $body
     * @param  string  $icon
     */
    public function __construct(string $title = null, mixed $body = '', string $icon = 'fas fa-info-circle')
    {
        parent::__construct();

        $this->title = $title;

        $this->icon = $icon;

        $this->body = $body;
    }

    /**
     * Set the title of the small box.
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
     * Set a small box icon.
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
     * Install the body of the small box.
     *
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
     * Additional data to be sent to the template.
     *
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
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
    }
}
