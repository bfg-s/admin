<?php

namespace Admin\Components\Fields;

class SwitcherField extends InputField
{
    /**
     * @var string
     */
    protected $type = 'checkbox';

    /**
     * @var string
     */
    protected $icon = null;

    /**
     * @var bool
     */
    protected $form_control = false;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'switch',
    ];

    /**
     * @param  string  $size
     * @return $this
     */
    public function switchSize(string $size)
    {
        $this->data['size'] = $size;

        return $this;
    }

    /**
     * @param  string  $on
     * @param  string  $off
     * @param  string  $label
     * @return $this
     */
    public function labels(string $on = null, string $off = null, string $label = null)
    {
        if ($on) {
            $this->data['on-text'] = $on;
        }
        if ($off) {
            $this->data['off-text'] = $off;
        }
        if ($label) {
            $this->data['label-text'] = $label;
        }

        return $this;
    }

    /**
     * On build.
     */
    protected function on_build()
    {
        if (!isset($this->data['on-text'])) {
            $this->data['on-text'] = __('admin.on');
        }

        if (!isset($this->data['off-text'])) {
            $this->data['off-text'] = __('admin.off');
        }
    }

    /**
     * @return int|mixed
     */
    protected function create_value()
    {
        if (parent::create_value()) {
            $this->params[] = ['checked' => 'true'];
        }

        return 1;
    }
}
