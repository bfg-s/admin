<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\BUTTON;
use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Segments\Tagable\Traits\TypesTrait;

/**
 * Class ModalFooterButton
 * @package Lar\LteAdmin\Segments\Tagable
 */
class ModalFooterButton extends BUTTON {

    use TypesTrait, FontAwesome;

    /**
     * @var array
     */
    protected $props = [
        'btn', 'btn-sm'
    ];

    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var string
     */
    protected $text;

    /**
     * Col constructor.
     * @param  string  $text
     * @param  mixed  ...$params
     */
    public function __construct(string $text = "", ...$params)
    {
        parent::__construct();

        $this->text = $text;

        $this->when($params);

        $this->toExecute('_build');
    }

    /**
     * @return $this
     */
    public function cancel()
    {
        if (request()->modal) {

            $this->on_click("modal:hide", request()->modal);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function destroy()
    {
        if (request()->modal) {

            $this->on_click("modal:destroy", request()->modal);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function toggle()
    {
        if (request()->modal) {

            $this->on_click("modal:toggle", request()->modal);
        }

        return $this;
    }

    /**
     * Build button
     */
    protected function _build()
    {
        $this->addClass("btn-" . $this->type);

        if ($this->icon) {

            $this->i([$this->icon], ':space');
        }

        if ($this->text) {

            $this->text($this->text);
        }
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
}