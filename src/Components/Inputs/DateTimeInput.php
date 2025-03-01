<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Traits\DateControlTrait;
use Illuminate\Support\Facades\App;

/**
 * Input the admin panel to select the date and time.
 */
class DateTimeInput extends Input
{
    use DateControlTrait;

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-calendar-plus';

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'picker::datetime',
        'toggle' => 'datetimepicker',
    ];

    /**
     * The autocomplete attribute of component.
     *
     * @var string
     */
    protected string $autocomplete = 'off';

    /**
     * DateTimeInput constructor.
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
