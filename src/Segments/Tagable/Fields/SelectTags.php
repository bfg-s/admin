<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class SelectTags
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class SelectTags extends Select
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-tags';

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  string  $id
     * @param  null  $value
     * @param  bool  $has_bug
     * @param  null  $path
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field(string $name, string $title, string $id = '', $value = null, bool $has_bug = false, $path = null)
    {
        return \Lar\LteAdmin\Components\Select2Tags::create($this->options, [
            'name' => $name,
            'data-placeholder' => $title
        ], ...$this->params)->when(function (\Lar\LteAdmin\Components\Select2Tags $input) use ($value) {
            $input->setValues($value ?? $this->value);
        })->makeOptions()
            ->setDatas($this->data)
            ->addClassIf($has_bug, 'is-invalid')
            ->addClass($this->class);
    }
}