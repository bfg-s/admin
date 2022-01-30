<?php

namespace LteAdmin\Components;

use Closure;
use Lar\Layout\Traits\FontAwesome;
use LteAdmin\Traits\TypesTrait;

/**
 * Class AlertComponent.
 * @mixin AlertComponentMacroList
 */
class AlertComponent extends Component
{
    use FontAwesome, TypesTrait;

    /**
     * @var string[]
     */
    protected $props = [
        'alert', 'role' => 'alert',
    ];

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var string|mixed
     */
    protected $body;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param  string  $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  string|array|Closure  $body
     * @return $this
     */
    public function body($body)
    {
        $this->body = $body;

        return $this;
    }

    protected function mount()
    {
        if ($this->title) {
            $h4 = $this->h4(['alert-heading']);

            if ($this->icon) {
                $h4->i([$this->icon]);
                $h4->text(':space');
            }

            if ($this->title) {
                $h4->text(__($this->title));
            }
        }

        if ($this->body) {
            $this->appEnd(is_string($this->body) ? __($this->body) : $this->body);
        }

        $this->addClass("alert-{$this->type}");
    }
}
