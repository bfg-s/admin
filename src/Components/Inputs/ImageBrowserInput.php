<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\InputGroupComponent;
use Admin\Components\Inputs\Vue\ImageBrowserVue;
use Illuminate\View\View;
use Throwable;

/**
 * Input admin panel for selecting and controlling many pictures.
 */
class ImageBrowserInput extends InputGroupComponent
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * Method for creating an input field.
     *
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
