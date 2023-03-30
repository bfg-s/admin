<?php

namespace Admin\Traits;

trait AlpineInjectionTrait
{
    protected array $next_modifiers = [];

    public function xInit($data = ''): static
    {
        return $this->xAttribute('init', $data);
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
            "x-{$name}".($modifiers ? '.'.implode('.', $modifiers) : ''),
            is_array($value) ? json_encode($value) : $value
        );

        return $this;
    }

    public function xData($data = ''): static
    {
        return $this->xAttribute('data', $data);
    }

    public function xShow(string $value = '')
    {
        return $this->xAttribute('show', $value);
    }

    public function xBind(string $attribute, string $value = '')
    {
        return $this->xAttribute("bind:$attribute", $value);
    }

    public function xText(string $value): static
    {
        return $this->xAttribute('text', $value);
    }

    public function xHtml(string $value): static
    {
        return $this->xAttribute('html', $value);
    }

    public function xModel(string $value): static
    {
        return $this->xAttribute('model', $value);
    }

    public function xFor(string $value): static
    {
        return $this->xAttribute('for', $value);
    }

    public function xTransition(): static
    {
        return $this->xAttribute('transition');
    }

    public function xEffect(string $value): static
    {
        return $this->xAttribute('effect', $value);
    }

    public function xIgnore(): static
    {
        return $this->xAttribute('ignore');
    }

    public function xRef(string $value): static
    {
        return $this->xAttribute('ref', $value);
    }

    public function xCloak(): static
    {
        return $this->xAttribute('cloak');
    }

    public function xTeleport(string $value): static
    {
        return $this->xAttribute('teleport', $value);
    }

    public function xIf(string $value): static
    {
        return $this->xAttribute('if', $value);
    }

    public function xId(string $value): static
    {
        return $this->xAttribute('id', $value);
    }

    public function xOnClick(string $value): static
    {
        return $this->xOn('click', $value);
    }

    public function xOn(string $event, string $value): static
    {
        return $this->xAttribute("on:{$event}", $value);
    }

    public function xOnSubmit(string $value): static
    {
        return $this->xOn('submit', $value);
    }

    public function xOnDblclick(string $value): static
    {
        return $this->xOn('dblclick', $value);
    }

    public function xOnChange(string $value): static
    {
        return $this->xOn('change', $value);
    }

    public function xOnBlur(string $value): static
    {
        return $this->xOn('blur', $value);
    }

    public function xOnFocus(string $value): static
    {
        return $this->xOn('focus', $value);
    }

    public function xOnFormchange(string $value): static
    {
        return $this->xOn('formchange', $value);
    }

    public function xOnForminput(string $value): static
    {
        return $this->xOn('forminput', $value);
    }

    public function xOnInput(string $value): static
    {
        return $this->xOn('input', $value);
    }

    public function xOnKeydown(string $value): static
    {
        return $this->xOn('keydown', $value);
    }

    public function xOnKeypress(string $value): static
    {
        return $this->xOn('keypress', $value);
    }

    public function xOnKeyup(string $value): static
    {
        return $this->xOn('keyup', $value);
    }

    public function xOnMousedown(string $value): static
    {
        return $this->xOn('mousedown', $value);
    }

    public function xOnMousemove(string $value): static
    {
        return $this->xOn('mousemove', $value);
    }

    public function xOnMouseout(string $value): static
    {
        return $this->xOn('mouseout', $value);
    }

    public function xOnMouseover(string $value): static
    {
        return $this->xOn('mouseover', $value);
    }

    public function xOnMouseup(string $value): static
    {
        return $this->xOn('mouseup', $value);
    }

    public function xOnMousewheel(string $value): static
    {
        return $this->xOn('mousewheel', $value);
    }

    public function xOnHover(string $value): static
    {
        return $this->xOn('hover', $value);
    }

    public function xOnLoad(string $value): static
    {
        return $this->xOn('load', $value);
    }
}
