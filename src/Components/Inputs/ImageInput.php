<?php

namespace Admin\Components\Inputs;

class ImageInput extends FileInput
{
    /**
     * @var string
     */
    protected $type = 'file';

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'file',
    ];

    /**
     * After construct event.
     */
    protected function after_construct(): void
    {
        $this->exts('jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp');
        $this->image();
    }
}
