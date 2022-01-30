<?php

namespace LteAdmin\Components\Fields;

class ImageField extends FileField
{
    /**
     * @var string
     */
    protected $type = 'file';

    /**
     * @var string
     */
    protected $icon = null;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'file',
    ];

    /**
     * After construct event.
     */
    protected function after_construct()
    {
        $this->exts('jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp');
        $this->image();
    }
}
