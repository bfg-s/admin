<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Components\Traits\TypesTrait;

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
    private $title;

    /**
     * @var string|null
     */
    private $icon;

    /**
     * @var string|mixed
     */
    private $body;

    /**
     * @var array
     */
    private $params;

    /**
     * @param  array  $title
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
     * @param  string|array|\Closure  $body
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
            $this->appEnd($this->body);
        }

        $this->addClass("alert-{$this->type}");
    }
}
