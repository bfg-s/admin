<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\FontAwesomeTrait;
use Admin\Traits\Typeable;

/**
 * Information box component of the admin panel.
 */
class InfoBoxComponent extends Component
{
    use FontAwesomeTrait;
    use Typeable;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'info-box';

    /**
     * Box title.
     *
     * @var string|null
     */
    private string|null $title = null;

    /**
     * Box icon.
     *
     * @var string|null
     */
    private string|null $icon = null;

    /**
     * @var string|array|null
     */
    private string|array|null $link = null;

    /**
     * Box body.
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
     * InfoBoxComponent constructor.
     *
     * @param  string|null  $title
     * @param  string  $body
     * @param  string  $icon
     */
    public function __construct(
        string $title = null,
        string $body = '',
        string $icon = 'fas fa-info-circle',
    ) {
        parent::__construct();

        $this->title = $title;

        $this->icon = $icon;

        $this->body = $body;
    }

    /**
     * Set the box title.
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
     * Set box icon.
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
     * Set a button with the specified box link.
     *
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
     * Install the box body.
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
            'body' => is_array($this->body) ? $this->body : [$this->body],
            'title' => $this->title,
            'icon' => $this->icon,
            'link' => $this->link ? (!is_array($this->link) ? [$this->link] : $this->link) : null,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
