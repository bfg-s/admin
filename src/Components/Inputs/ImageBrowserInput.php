<?php

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Admin\Components\Inputs\Vue\BrowserVue;
use Illuminate\View\View;

class ImageBrowserInput extends FormGroupComponent
{
    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var bool
     */
    protected bool $form_control = true;

    /**
     * @var int|null
     */
    protected ?int $rows = null;

    /**
     * @return View
     * @throws \Throwable
     */
    public function field(): string
    {
        return (new BrowserVue)
            ->attr('value', $this->value)
            ->attr('field-name', $this->name)
            ->render();
    }
}
