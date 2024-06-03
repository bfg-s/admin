<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Traits\DateControlTrait;
use Illuminate\Support\Facades\App;

/**
 * Input admin panel for entering time.
 */
class TimeInput extends Input
{
    use DateControlTrait;

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected string|null $icon = 'fas fa-clock';

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'picker::time',
        'toggle' => 'datetimepicker',
    ];

    /**
     * The autocomplete attribute of component.
     *
     * @var string
     */
    protected string $autocomplete = 'off';

    /**
     * TimeInput constructor.
     *
     * @param  string  $name
     * @param  string|null  $title
     * @param ...$params
     */
    public function __construct(string $name, string $title = null, ...$params)
    {
        parent::__construct($name, $title, $params);

        $this->data['load-params'] = [App::getLocale()];
    }

    /**
     * Method to override, event before creating the input wrapper.
     *
     * @return void
     */
    protected function on_build(): void
    {
        $this->data['target'] = "#{$this->field_id}";
    }
}
