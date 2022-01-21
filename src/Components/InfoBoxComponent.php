<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Components\Traits\TypesTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;

/**
 * @mixin InfoBoxComponentMacroList
 */
class InfoBoxComponent extends DIV
{
    use FontAwesome, TypesTrait, Macroable, Delegable;

    /**
     * @var string[]
     */
    protected $props = [
        'small-box',
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
     * @var string|null
     */
    private $link;

    /**
     * @var string|mixed
     */
    private $body;

    /**
     * @var array
     */
    private $params;

    /**
     * Alert constructor.
     * @param  string|null  $title
     * @param  string|null  $icon
     * @param $body
     * @param  mixed  ...$params
     */
    public function __construct(string $title = null, $body = '', string $icon = 'fas fa-info-circle', ...$params)
    {
        parent::__construct();

        $this->title = $title;

        $this->icon = $icon;

        $this->body = $body;

        $this->params = $params;

        $this->toExecute('_build');

        $this->callConstructEvents();
    }

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
     * @param  string  $link
     * @param  string|null  $text
     * @param  string|null  $icon
     * @return $this
     */
    public function link(string $link, string $text = null, string $icon = null)
    {
        $this->link = [$link, $text, $icon];

        return $this;
    }

    /**
     * @param  string|array  $body
     * @param  string  $small_info
     * @return $this
     */
    public function body($body, $small_info = '')
    {
        $this->body = [$body, $small_info];

        return $this;
    }

    /**
     * Build alert.
     */
    protected function _build()
    {
        $this->callRenderEvents();

        $this->addClass("bg-{$this->type}");

        $inner = $this->div(['inner']);

        if ($this->body) {
            $this->body = is_array($this->body) ? $this->body : [$this->body];
            $inner->h3()
                ->text(' '.($this->body[0] ?? ''))
                ->sup(['style' => 'font-size: 20px'])->text(' '.($this->body[1] ?? ''));
        }

        if ($this->title) {
            $inner->p($this->title);
        }

        if ($this->icon) {
            $this->div(['icon'])->i([$this->icon]);
        }

        if ($this->link) {
            $link = ! is_array($this->link) ? [$this->link] : $this->link;
            $a = $this->a(['small-box-footer'])->setHrefIf(isset($link[0]), $link[0]);
            $a->text($link[1] ?? __('lte.more_info'), ':space');
            $a->i([$link[2] ?? 'fas fa-arrow-circle-right']);
        }

        $this->when($this->params);
    }
}
