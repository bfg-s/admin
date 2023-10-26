<?php

namespace Admin\Components\Inputs;

class NumericInput extends Input
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-hashtag';

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'mask',
        'load-params' => '-{0,1}9{0,}',
    ];
}
