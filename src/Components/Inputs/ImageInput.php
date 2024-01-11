<?php

namespace Admin\Components\Inputs;

class ImageInput extends FileInput
{
    /**
     * After construct event.
     */
    protected function after_construct(): void
    {
        parent::after_construct();
        $this->exts('jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp');
        $this->image();
    }
}
