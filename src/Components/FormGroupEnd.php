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
     * @param  string|null  $info
     * @param  int  $label_width
     * @param  mixed  ...$params
     */
    public function __construct(string $name = null, \Illuminate\Support\ViewErrorBag $errors = null, bool $vertical = false, string $info = null, int $label_width = 2, ...$params)
    {
        parent::__construct();

        $this->when($params);

        $c = $vertical ? '' : 'col-sm-'.(12-$label_width);

        if ($info) {

            $this->text((!$vertical ? "<div class='col-sm-{$label_width}'></div>" : '')."<small class='text-primary invalid-feedback d-block {$c}'><i class='fas fa-info-circle'></i> {$info}</small>");
        }

        if ($name && $errors && $errors->has($name)) {

            $messages = $errors->get($name);

            foreach ($messages as $mess) {

                $this->text((!$vertical ? "<div class='col-sm-{$label_width}'></div>" : '')."<small class='error invalid-feedback d-block {$c}'><small class='fas fa-exclamation-triangle'></small> {$mess}</small>");
            }
        }
    }
}