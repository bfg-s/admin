<?php

declare(strict_types=1);

namespace Admin\Traits;

/**
 * Trait for combining components with AlpineJs capabilities.
 */
trait ComponentAlpineJsTrait
{
    /**
     * Modifiers for the next attribute addition.
     *
     * @var array
     */
    protected array $next_modifiers = [];

    /**
     * Add the specified X attribute.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  array  $modifiers
     * @return $this
     */
    public function xAttribute(string $name, mixed $value = '', array $modifiers = []): static
    {
        $modifiers = array_merge($this->next_modifiers, $modifiers);
        $this->next_modifiers = [];
        $this->attr(
            "x-{$name}".($modifiers ? '.'.implode('.', $modifiers) : ''), $value
        );

        return $this;
    }

    /**
     * Get the value of the X attribute.
     *
     * @param  string  $name
     * @param  array  $modifiers
     * @return mixed
     */
    public function xGet(string $name, array $modifiers = []): mixed
    {
        return $this->attributes["x-{$name}".($modifiers ? '.'.implode('.', $modifiers) : '')]
            ?? ($this->attributes["x-on:{$name}".($modifiers ? '.'.implode('.', $modifiers) : '')] ?? null);
    }

    /**
     * Check whether the specified X attribute exists.
     *
     * @param  string  $name
     * @param  array  $modifiers
     * @return bool
     */
    public function xHas(string $name, array $modifiers = []): bool
    {
        return isset($this->attributes["x-{$name}".($modifiers ? '.'.implode('.', $modifiers) : '')])
            || isset($this->attributes["x-on:{$name}".($modifiers ? '.'.implode('.', $modifiers) : '')]);
    }

    /**
     * Add X attribute "show".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xShow(mixed $value = ''): static
    {
        return $this->xAttribute('show', $value);
    }

    /**
     * Add X attribute "bind".
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return $this
     */
    public function xBind(string $attribute, mixed $value = ''): static
    {
        return $this->xAttribute("bind:$attribute", $value);
    }

    /**
     * Add X attribute "text".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xText(mixed $value): static
    {
        return $this->xAttribute('text', $value);
    }

    /**
     * Add X attribute "html".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xHtml(mixed $value): static
    {
        return $this->xAttribute('html', $value);
    }

    /**
     * Add X attribute "model".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xModel(mixed $value): static
    {
        return $this->xAttribute('model', $value);
    }

    /**
     * Add X attribute "for".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xFor(mixed $value): static
    {
        return $this->xAttribute('for', $value);
    }

    /**
     * Add X attribute "transition".
     *
     * @return $this
     */
    public function xTransition(): static
    {
        return $this->xAttribute('transition');
    }

    /**
     * Add X attribute "effect".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xEffect(mixed $value): static
    {
        return $this->xAttribute('effect', $value);
    }

    /**
     * Add X attribute "ignore".
     *
     * @return $this
     */
    public function xIgnore(): static
    {
        return $this->xAttribute('ignore');
    }

    /**
     * Add X attribute "ref".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xRef(mixed $value): static
    {
        return $this->xAttribute('ref', $value);
    }

    /**
     * Add X attribute "cloak".
     *
     * @return $this
     */
    public function xCloak(): static
    {
        return $this->xAttribute('cloak');
    }

    /**
     * Add X attribute "teleport".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xTeleport(mixed $value): static
    {
        return $this->xAttribute('teleport', $value);
    }

    /**
     * Add X attribute "if".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xIf(mixed $value): static
    {
        return $this->xAttribute('if', $value);
    }

    /**
     * Add X attribute "id".
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xId(mixed $value): static
    {
        return $this->xAttribute('id', $value);
    }

    /**
     * Add X attribute "data".
     *
     * @param  mixed  $data
     * @return $this
     */
    public function xData(mixed $data = ''): static
    {
        return $this->xAttribute('data', $data);
    }

    /**
     * Add the specified X event.
     *
     * @param  string  $event
     * @param  mixed  $value
     * @return $this
     */
    public function xOn(string $event, mixed $value): static
    {
        $this->xInit();

        return $this->xAttribute("on:{$event}", $value);
    }

    /**
     * Add X event to init.
     *
     * @param  mixed  $data
     * @return $this
     */
    public function xInit(mixed $data = ''): static
    {
        if (!$this->xHas('init')) {
            $this->xAttribute('init');

            return $data ? $this->xData($data) : $this;
        }

        return $this;
    }

    /**
     * Add X event on click.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnClick(mixed $value): static
    {
        return $this->xOn('click', $value);
    }

    /**
     * Add X event on submit.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnSubmit(mixed $value): static
    {
        return $this->xOn('submit', $value);
    }

    /**
     * Add X event on dblclick.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnDblclick(mixed $value): static
    {
        return $this->xOn('dblclick', $value);
    }

    /**
     * Add X event on change.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnChange(mixed $value): static
    {
        return $this->xOn('change', $value);
    }

    /**
     * Add X event on blur.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnBlur(mixed $value): static
    {
        return $this->xOn('blur', $value);
    }

    /**
     * Add X event on focus.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnFocus(mixed $value): static
    {
        return $this->xOn('focus', $value);
    }

    /**
     * Add X event on formchange.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnFormchange(mixed $value): static
    {
        return $this->xOn('formchange', $value);
    }

    /**
     * Add X event on forminput.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnForminput(mixed $value): static
    {
        return $this->xOn('forminput', $value);
    }

    /**
     * Add X event on input.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnInput(mixed $value): static
    {
        return $this->xOn('input', $value);
    }

    /**
     * Add X event on keydown.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnKeydown(mixed $value): static
    {
        return $this->xOn('keydown', $value);
    }

    /**
     * Add X event on keypress.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnKeypress(mixed $value): static
    {
        return $this->xOn('keypress', $value);
    }

    /**
     * Add X event on keyup.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnKeyup(mixed $value): static
    {
        return $this->xOn('keyup', $value);
    }

    /**
     * Add X event on mousedown.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMousedown(mixed $value): static
    {
        return $this->xOn('mousedown', $value);
    }

    /**
     * Add X event on mousemove.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMousemove(mixed $value): static
    {
        return $this->xOn('mousemove', $value);
    }

    /**
     * Add X event on mouseout.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMouseout(mixed $value): static
    {
        return $this->xOn('mouseout', $value);
    }

    /**
     * Add X event on mouseover.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMouseover(mixed $value): static
    {
        return $this->xOn('mouseover', $value);
    }

    /**
     * Add X event on mouseup.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMouseup(mixed $value): static
    {
        return $this->xOn('mouseup', $value);
    }

    /**
     * Add X event on mousewheel.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMousewheel(mixed $value): static
    {
        return $this->xOn('mousewheel', $value);
    }

    /**
     * Add X event on hover.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnHover(mixed $value): static
    {
        return $this->xOn('hover', $value);
    }

    /**
     * Add X event on load.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function xOnLoad(mixed $value): static
    {
        return $this->xOn('load', $value);
    }
}
