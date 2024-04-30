<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\FormGroupComponent;
use Admin\Components\Inputs\Vue\ImageBrowserVue;
use Illuminate\View\View;
use Throwable;

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
     * @throws Throwable
     */
    public function field(): string
    {
        return (new ImageBrowserVue)
            ->attr('value', $this->value)
            ->attr('field-name', $this->name)
            ->render();
    }
}
