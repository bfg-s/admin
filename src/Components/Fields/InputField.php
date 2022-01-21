<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\FormGroupComponent;

class InputField extends FormGroupComponent
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
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return \Lar\Layout\Tags\INPUT::create([
            'type' => $this->type,
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title,
        ], ...$this->params)
            ->setValue($this->value)
            ->setRules($this->rules)
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid')
            ->addClassIf($this->form_control, 'form-control');
    }

    /**
     * @return $this
     */
    public function slugable()
    {
        $this->on_keyup('str::slug');

        return $this;
    }

    /**
     * @param  string  $to
     * @return $this
     */
    public function duplication_how_slug(string $to)
    {
        $this->on_keyup('str::slug', $to);

        return $this;
    }

    /**
     * @param  string  $to
     * @return $this
     */
    public function duplication(string $to)
    {
        $this->on_keyup('$::val', "{$to} && >>$:val");

        return $this;
    }

    /**
     * @param  string  $mask
     * @return $this
     */
    public function mask(string $mask)
    {
        $this->on_load('mask', $mask);

        return $this;
    }

    /**
     * @return $this
     */
    public function disabled()
    {
        $this->params[] = ['disabled' => 'true'];

        return $this;
    }
}
