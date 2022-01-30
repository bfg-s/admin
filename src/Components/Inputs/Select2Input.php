<?php

namespace LteAdmin\Components\Inputs;

use Illuminate\Support\Collection;

class Select2Input extends InputComponent
{
    protected ?Collection $options = null;

    public function options($options, bool $firstDefault = false)
    {
        $this->options = collect($options);

        if ($firstDefault) {
            $this->default($this->options->first());
        }

        return $this;
    }

    protected function mount()
    {
        $this->view('lte::inputs.select2', [
            'options' => $this->options,
            'default' => $this->default,
        ]);
    }

    protected function javascript()
    {
        return <<<JS
select2 = $(\$refs.select)
    .not('.select2-hidden-accessible')
    .select2({
        width: '100%',
        theme: 'bootstrap4'
    });
select2.on('select2:select', (event) => {
    value = $(event.target).val();
});
select2.on('select2:unselect', (event) => {
    value = $(event.target).val();
});
\$watch('value', (val) => {
    select2.val(val).trigger('change');
    $this->store = val;
});
JS;
    }
}
