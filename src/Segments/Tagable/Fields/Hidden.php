<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Hidden.
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Hidden extends FormGroup
{
    /**
     * @var string
     */
    protected $type = 'hidden';

    /**
     * @var bool
     */
    protected $vertical = true;

    /**
     * @var null
     */
    protected $icon = null;

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
            ->setValue($this->value);
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
