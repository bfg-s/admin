<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\Component;
use Admin\Components\DummyComponent;
use Admin\Components\FieldComponent;
use Admin\Components\Small\DivComponent;
use Admin\Components\WidgetComponent;
use Admin\Core\Delegate;
use Admin\Exceptions\WidgetErrorException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;

/**
 * General class for creating widgets on the dashboard.
 */
abstract class WidgetAbstract implements Renderable, Arrayable
{
    /**
     * The ID of the widget.
     *
     * @var string|int|float|null
     */
    protected string|int|float|null $id = null;

    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = null;

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = null;

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = null;

    /**
     * Settings for the widget.
     *
     * @var array
     */
    protected array $settings = [];

    /**
     * Settings description for the widget.
     *
     * @var array
     */
    protected array $settingsDescription = [];

    /**
     * Settings type for the widget.
     *
     * @var array
     */
    protected array $settingsType = [];

    /**
     * Limiting the widget by roles.
     *
     * @var array
     */
    protected array $roles = [];

    /**
     * WidgetAbstract constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if ($key === 'settings') {
                $this->settings($value);
            } else if ($key !== 'class') {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Merge the given settings with the current settings.
     *
     * @param  array  $settings
     * @return $this
     */
    public function settings(array $settings): static
    {
        $this->settings = array_merge($this->settings, $settings);

        return $this;
    }

    /**
     * Check if the widget has access to the current user.
     *
     * @return bool
     */
    public function isHasAccess(): bool
    {
        foreach (admin()->roles as $role) {

            if (in_array($role->slug, $this->roles) || ! $this->roles) {

                return true;
            }
        }

        return false;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     * @throws \Throwable
     */
    public function render(): mixed
    {
        if (! method_exists($this, 'handle')) {

            return '';
        }

        $component = (new WidgetComponent)->setSettings($this->settings);

        return embedded_call([$this, 'handle'], [
            $component::class => $component
        ]);

        if (! $call) {

            return '';
        }

        if ($call instanceof Component) {
            $div = $call;
        } else {
            $div = WidgetComponent::create()
                ->newExplainForce($call);
        }

        return $div->render();
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     * @throws \Throwable
     */
    public function toArray(): array
    {
        return [
            'class' => static::class,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'settings' => $this->settings,
            'settingsDescription' => $this->settingsDescription,
            'settingsType' => $this->settingsType,
        ];
    }

    /**
     * Export widget for db write.
     *
     * @return array
     */
    public function export(): array
    {
        return [
            'id' => $this->id ?: ((int) (crc32(uniqid()) . crc32(uniqid()))),
            'class' => static::class,
            'settings' => $this->settings,
        ];
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     * @throws \JsonException
     * @throws \Throwable
     */
    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
    }

    /**
     * A default method to create a new instance of the widget.
     *
     * @param  array  $attributes
     * @return static
     */
    public static function create(array $attributes = []): static
    {
        return new static($attributes);
    }
}
