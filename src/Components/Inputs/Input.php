<?php

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Illuminate\View\View;

class Input extends FormGroupComponent
{
    /**
     * @var string
     */
    protected $type = 'text';

    /**
     * @var bool
     */
    protected $form_control = true;

    /**
     * @var string
     */
    protected string $autocomplete = '';

    /**
     * @var array
     */
    protected array $attributes = [];

    /**
     * @var bool
     */
    protected bool $checked = false;

    /**
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
     * @return $this
     */
    public function slugable(): static
    {
        $this->on_keyup('str::slug');

        return $this;
    }

    /**
     * @param  string  $to
     * @return $this
     */
    public function duplication_how_slug(string $to): static
    {
        $this->on_keyup('str::slug', $to);

        return $this;
    }

    /**
     * @param  string  $to
     * @return $this
     */
    public function duplication(string $to): static
    {
        $this->on_keyup('$::val', "{$to} && >>$:val");

        return $this;
    }

    /**
     * @param  string  $mask
     * @return $this
     */
    public function mask(string $mask): static
    {
        $this->on_load('mask', $mask);

        return $this;
    }

    /**
     * @return $this
     */
    public function disabled(): static
    {
        $this->attributes['disabled'] = 'true';

        return $this;
    }
}
