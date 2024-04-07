<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

class SwitcherInput extends Input
{
    /**
     * @var string
     */
    protected $type = 'checkbox';

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var bool
     */
    protected $form_control = false;

    /**
     * @var array
     */
    protected array $data = [
        'load' => 'bootstrapSwitch'
    ];

    /**
     * @param  string  $size
     * @return $this
     */
    public function switchSize(string $size): static
    {
        $this->data['size'] = $size;

        return $this;
    }

    /**
     * @param  string|null  $on
     * @param  string|null  $off
     * @param  string|null  $label
     * @return $this
     */
    public function labels(string $on = null, string $off = null, string $label = null): static
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
     * @return void
     */
    protected function on_build(): void
    {
        if (!isset($this->data['on-text'])) {
            $this->data['on-text'] = __('admin.on');
        }

        if (!isset($this->data['off-text'])) {
            $this->data['off-text'] = __('admin.off');
        }
    }

    /**
     * @return int
     */
    protected function create_value(): int
    {
        if (parent::create_value()) {
            //$this->params[] = ['checked' => 'true'];
            $this->checked = true;
        }

        return 1;
    }
}
