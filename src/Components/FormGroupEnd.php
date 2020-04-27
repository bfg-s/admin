<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Abstracts\Component;

/**
 * Class FormGroupEnd
 * @package Lar\LteAdmin\Components
 */
class FormGroupEnd extends Component
{
    /**
     * FormGroupEnd constructor.
     * @param  string|null  $name
     * @param  \Illuminate\Support\ViewErrorBag  $errors
     * @param  bool  $vertical
     * @param  mixed  ...$params
     */
    public function __construct(string $name = null, \Illuminate\Support\ViewErrorBag $errors = null, bool $vertical = false, ...$params)
    {
        parent::__construct();

        $this->when($params);

        if ($name && $errors && $errors->has($name)) {

            $messages = $errors->get($name);

            $c = $vertical ? '' : 'col-sm-10';

            foreach ($messages as $mess) {

                $this->text((!$vertical ? "<div class='col-sm-2'></div>" : '')."<small class='error invalid-feedback d-block {$c}'>{$mess}</small>");
            }
        }
    }
}