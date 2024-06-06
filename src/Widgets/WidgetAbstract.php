<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\FieldComponent;
use Admin\Exceptions\WidgetErrorException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;

/**
 * General class for creating widgets on the dashboard.
 */
abstract class WidgetAbstract implements Renderable, Arrayable
{
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
     * The slug of the widget.
     *
     * @var string
     */
    protected string $slug;

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
     * WidgetAbstract constructor.
     *
     * @throws WidgetErrorException
     */
    public function __construct()
    {
        if (! method_exists($this, 'handle')) {

            throw new WidgetErrorException('The handle method is not defined in the widget.');
        }
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     * @throws \Throwable
     */
    public function render(): string
    {
        return FieldComponent::create()
            ->newExplainForce(app()->call([$this, 'handle']))
            ->render();
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
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'slug' => $this->slug,
            'settings' => $this->settings,
            'settingsDescription' => $this->settingsDescription,
            'settingsType' => $this->settingsType,
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
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * A default method to create a new instance of the widget.
     *
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }
}
