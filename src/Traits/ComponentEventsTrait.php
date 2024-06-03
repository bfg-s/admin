<?php

namespace Admin\Traits;

use Admin\Respond;

/**
 * A trait helper that adds methods for adding event date attributes.
 */
trait ComponentEventsTrait
{
    /**
     * Add event "click" on component.
     *
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
     * Add event "submit" on component.
     *
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
     * Add event "dblclick" on component.
     *
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
     * Add event "change" on component.
     *
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
     * Add event "blur" on component.
     *
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
     * Add event "focus" on component.
     *
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
     * Add event "formchange" on component.
     *
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
     * Add event "forminput" on component.
     *
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
     * Add event "input" on component.
     *
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
     * Add event "keydown" on component.
     *
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
     * Add event "keypress" on component.
     *
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
     * Add event "keyup" on component.
     *
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
     * Add event "mousedown" on component.
     *
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
     * Add event "mousemove" on component.
     *
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
     * Add event "mouseout" on component.
     *
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
     * Add event "mouseover" on component.
     *
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
     * Add event "mouseup" on component.
     *
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
     * Add event "mousewheel" on component.
     *
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
     * Add event "hover" on component.
     *
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
     * Add event "load" on component.
     *
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
