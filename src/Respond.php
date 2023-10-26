<?php

namespace Admin;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Respond extends Collection implements Renderable, Htmlable
{
    /**
     * Global instance.
     *
     * @var Respond|null
     */
    private static ?Respond $instance_glob = null;

    /**
     * @var bool
     */
    protected bool $renderWithExecutor = false;

    /**
     * @param  array  $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    /**
     * @param  string|null  $link
     * @return $this
     */
    public function location(string $link = null): static
    {
        $this->put('location', $link);

        return $this;
    }

    /**
     * @param  string|null  $link
     * @return $this
     */
    public function redirect(string $link = null): static
    {
        $this->put('redirect', $link);

        return $this;
    }

    /**
     * @param  string|null  $link
     * @return $this
     */
    public function reload(string $link = null): static
    {
        $this->put('reload', $link);

        return $this;
    }

    /**
     * @param  string|null  $link
     * @return $this
     */
    public function reboot(string $link = null): static
    {
        $this->put('reboot', $link);

        return $this;
    }

    /**
     * toast:success.
     *
     * @param $text
     * @param  null  $title
     * @return $this
     */
    public function toast_success($text, $title = null): static
    {
        return $this->toast('success', $text, $title);
    }

    /**
     * toast:warning.
     *
     * @param $text
     * @param  null  $title
     * @return $this
     */
    public function toast_warning($text, $title = null): static
    {
        return $this->toast('warning', $text, $title);
    }

    /**
     * toast:info.
     *
     * @param $text
     * @param  null  $title
     * @return $this
     */
    public function toast_info($text, $title = null): static
    {
        return $this->toast('info', $text, $title);
    }

    /**
     * toast:error.
     *
     * @param $text
     * @param  null  $title
     * @return $this
     */
    public function toast_error($text, $title = null): static
    {
        return $this->toast('error', $text, $title);
    }

    /**
     * @param $type
     * @param $text
     * @param  null  $title
     * @return $this
     */
    public function toast($type, $text, $title = null): static
    {
        if (is_string($text)) {
            $this->put("toast::{$type}", $title ? [[__($text), __($title)]] : __($text));
        } else {
            $this->put("toast::{$type}", $text);
        }

        return $this;
    }

    /**
     * Put rule.
     *
     * @param $key
     * @param  mixed  $value
     * @return Respond
     */
    public function put($key, $value = null): static
    {
        if (is_array($key)) {
            foreach ($key as $name => $item) {
                $this->put($name, $item);
            }
            return $this;
        }
        return parent::put($this->count().':'.$key, $value);
    }

    /**
     * Access to global instance.
     *
     * @return Respond
     */
    public static function glob(): static
    {
        if (! static::$instance_glob) {

            static::$instance_glob = new static();
        }

        return static::$instance_glob;
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        return $this->toJson();
    }

    /**
     * @param  int  $options
     * @return string
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE): string
    {
        return parent::toJson($options);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        if ($this->renderWithExecutor) {

            return "window.exec(" . $this->toJson() . ")";
        }
        return $this->toJson();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return $this
     */
    public function renderWithExecutor(): static
    {
        $this->renderWithExecutor = true;

        return $this;
    }

    /**
     * @param ...$attributes
     * @return static
     */
    public static function create(...$attributes): static
    {
        return new static(...$attributes);
    }
}
