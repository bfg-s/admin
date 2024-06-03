<?php

declare(strict_types=1);

namespace Admin;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

/**
 * A class that is responsible for collecting commands that need to be executed on the front end.
 */
class Respond extends Collection implements Renderable, Htmlable
{
    /**
     * Global instance of respond.
     *
     * @var Respond|null
     */
    private static Respond|null $instance_glob = null;

    /**
     * A flag that determines whether the wrapper will be an executor function on the front end.
     *
     * @var bool
     */
    protected bool $renderWithExecutor = false;

    /**
     * Access to global instance.
     *
     * @return Respond
     */
    public static function glob(): static
    {
        if (!static::$instance_glob) {
            static::$instance_glob = static::create();
        }

        return static::$instance_glob;
    }

    /**
     * Create new respond instance.
     *
     * @param ...$attributes
     * @return static
     */
    public static function create(...$attributes): static
    {
        return new static(...$attributes);
    }

    /**
     * Redirects to the specified address.
     *
     * @param  string|null  $link
     * @return $this
     */
    public function location(string $link = null): static
    {
        $this->put('location', $link);

        return $this;
    }

    /**
     * Add an arbitrary command to be executed by the client.
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
     * Redirects to the specified address and reloads the page.
     *
     * @param  string|null  $link
     * @return $this
     */
    public function redirect(string $link = null): static
    {
        $this->put('redirect', $link);

        return $this;
    }

    /**
     * Reloads content on the page.
     *
     * @param  string|null  $link
     * @return $this
     */
    public function reload(string $link = null): static
    {
        $this->put('reload', $link);

        return $this;
    }

    /**
     * Reloads the page with a real page reload.
     *
     * @param  string|null  $link
     * @return $this
     */
    public function reboot(string $link = null): static
    {
        $this->put('reboot', $link);

        return $this;
    }

    /**
     * Sends a message indicating the operation was successful.
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
     * Send a toast message.
     *
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
     * Sends a warning message.
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
     * Sends an informational message.
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
     * Sends an error message.
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
     * Convert command library to string for HTML.
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->toJson();
    }

    /**
     * Convert command library to Json.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE): string
    {
        return parent::toJson($options);
    }

    /**
     * Convert command library to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Render a library of commands for execution.
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->renderWithExecutor) {
            return "window.exec(".$this->toJson().")";
        }
        return $this->toJson();
    }

    /**
     * Render a library of commands with a front-end executor function.
     *
     * @return $this
     */
    public function renderWithExecutor(): static
    {
        $this->renderWithExecutor = true;

        return $this;
    }
}
