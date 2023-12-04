<?php

namespace Admin\Traits;

use Admin\Respond;
use Illuminate\Contracts\Support\Renderable;

trait DataTrait
{
    /**
     * Data rules.
     * @var array
     */
    protected array $data = [];

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_click(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-click',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_submit(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-submit',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_dblclick(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-dblclick',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_change(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-change',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_blur(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-blur',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_focus(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-focus',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_formchange(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-formchange',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_forminput(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-forminput',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_input(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-input',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_keydown(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-keydown',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_keypress(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-keypress',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_keyup(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-keyup',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mousedown(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-mousedown',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mousemove(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-mousemove',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mouseout(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-mouseout',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mouseover(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-mouseover',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mouseup(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-mouseup',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mousewheel(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-mousewheel',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_hover(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-hover',
            Respond::create()->put($command, $value)
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_load(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-load',
            Respond::create()->put($command, $value)
        );
    }
}
