<?php

declare(strict_types=1);

namespace Admin\Traits;

trait AlpineInjectionTrait
{
    /**
     * @var array
     */
    protected array $next_modifiers = [];

    /**
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
     * @param  mixed  $value
     * @return $this
     */
    public function xShow(mixed $value = ''): static
    {
        return $this->xAttribute('show', $value);
    }

    /**
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return $this
     */
    public function xBind(string $attribute, mixed $value = ''): static
    {
        return $this->xAttribute("bind:$attribute", $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xText(mixed $value): static
    {
        return $this->xAttribute('text', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xHtml(mixed $value): static
    {
        return $this->xAttribute('html', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xModel(mixed $value): static
    {
        return $this->xAttribute('model', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xFor(mixed $value): static
    {
        return $this->xAttribute('for', $value);
    }

    /**
     * @return $this
     */
    public function xTransition(): static
    {
        return $this->xAttribute('transition');
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xEffect(mixed $value): static
    {
        return $this->xAttribute('effect', $value);
    }

    /**
     * @return $this
     */
    public function xIgnore(): static
    {
        return $this->xAttribute('ignore');
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xRef(mixed $value): static
    {
        return $this->xAttribute('ref', $value);
    }

    /**
     * @return $this
     */
    public function xCloak(): static
    {
        return $this->xAttribute('cloak');
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xTeleport(mixed $value): static
    {
        return $this->xAttribute('teleport', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xIf(mixed $value): static
    {
        return $this->xAttribute('if', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xId(mixed $value): static
    {
        return $this->xAttribute('id', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnClick(mixed $value): static
    {
        return $this->xOn('click', $value);
    }

    /**
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
     * @param  mixed  $data
     * @return $this
     */
    public function xData(mixed $data = ''): static
    {
        return $this->xAttribute('data', $data);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnSubmit(mixed $value): static
    {
        return $this->xOn('submit', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnDblclick(mixed $value): static
    {
        return $this->xOn('dblclick', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnChange(mixed $value): static
    {
        return $this->xOn('change', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnBlur(mixed $value): static
    {
        return $this->xOn('blur', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnFocus(mixed $value): static
    {
        return $this->xOn('focus', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnFormchange(mixed $value): static
    {
        return $this->xOn('formchange', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnForminput(mixed $value): static
    {
        return $this->xOn('forminput', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnInput(mixed $value): static
    {
        return $this->xOn('input', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnKeydown(mixed $value): static
    {
        return $this->xOn('keydown', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnKeypress(mixed $value): static
    {
        return $this->xOn('keypress', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnKeyup(mixed $value): static
    {
        return $this->xOn('keyup', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMousedown(mixed $value): static
    {
        return $this->xOn('mousedown', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMousemove(mixed $value): static
    {
        return $this->xOn('mousemove', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMouseout(mixed $value): static
    {
        return $this->xOn('mouseout', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMouseover(mixed $value): static
    {
        return $this->xOn('mouseover', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMouseup(mixed $value): static
    {
        return $this->xOn('mouseup', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnMousewheel(mixed $value): static
    {
        return $this->xOn('mousewheel', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnHover(mixed $value): static
    {
        return $this->xOn('hover', $value);
    }

    /**
     * @param  mixed  $value
     * @return $this
     */
    public function xOnLoad(mixed $value): static
    {
        return $this->xOn('load', $value);
    }
}
