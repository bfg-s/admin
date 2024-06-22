<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\FontAwesomeTrait;
use Admin\Traits\Typeable;
use Closure;

/**
 * Alert component for outputting a message.
 */
class AlertComponent extends Component
{
    use FontAwesomeTrait;
    use Typeable;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'alert';

    /**
     * Component title.
     *
     * @var string|null
     */
    protected string|null $title = null;

    /**
     * Component icon.
     *
     * @var string|null
     */
    protected string|null $icon = null;

    /**
     * Component message body.
     *
     * @var string|mixed
     */
    protected mixed $body = null;

    /**
     * Set the component title.
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
     * Set the component icon.
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
     * Set the message body of the component.
     *
     * @param  array|string|Closure  $body
     * @return $this
     */
    public function body(array|string|Closure $body): static
    {
        $this->body = $body;

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
            'title' => __($this->title),
            'icon' => $this->icon,
            'type' => $this->type,
            'body' => is_string($this->body) ? __($this->body) : $this->body
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
