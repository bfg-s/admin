<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Illuminate\View\View;

/**
 * Input the admin panel to display simple data.
 */
class Input extends InputGroupComponent
{
    /**
     * HTML attribute of the input type.
     *
     * @var string
     */
    protected $type = 'text';

    /**
     * Added or not a form control class for input.
     *
     * @var bool
     */
    protected bool $form_control = true;

    /**
     * The autocomplete attribute of component.
     *
     * @var string
     */
    protected string $autocomplete = '';

    /**
     * List of attributes that need to be added to the input.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * Whether the current input is checked or not.
     *
     * @var bool
     */
    protected bool $checked = false;

    /**
     * Method for creating an input field.
     *
     * @return View
     */
    public function field(): View
    {
        return admin_view('components.inputs.input', [
            'type' => $this->type,
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title,
            'value' => $this->value,
            'rules' => $this->rules,
            'datas' => array_merge($this->data, [
                'id' => $this->model?->id,
                'model' => $this->model ? get_class($this->model) : null,
                'field' => $this->path,
            ]),
            'has_bug' => $this->has_bug,
            'autocomplete' => $this->autocomplete,
            'form_control' => $this->form_control,
            'attributes' => $this->attributes,
            'checked' => $this->checked,
        ]);
    }

    /**
     * Transform the input data into a slug.
     *
     * @return $this
     */
    public function slugable(): static
    {
        $this->on_keyup('str::slug');

        return $this;
    }

    /**
     * Duplicate input data into another input as a slug.
     *
     * @param  string  $to
     * @return $this
     */
    public function duplication_how_slug(string $to): static
    {
        $this->on_keyup('str::slug', $to);

        return $this;
    }

    /**
     * Duplicate input data into another input.
     *
     * @param  string  $to
     * @return $this
     */
    public function duplication(string $to): static
    {
        $this->on_keyup('str::copy', $to);

        return $this;
    }

    /**
     * Set the mask to the current input.
     *
     * @param  mixed  $mask
     * @param  array  $options
     * @return $this
     */
    public function mask(mixed $mask, array $options = []): static
    {
        $this->on_load('mask', [$mask, $options]);

        return $this;
    }

    /**
     * Disable current input.
     *
     * @return $this
     */
    public function disabled(): static
    {
        $this->attributes['disabled'] = 'true';

        return $this;
    }
}
