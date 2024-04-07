<?php

declare(strict_types=1);

namespace Admin\Components\Small;

use Admin\Components\Component;

class ImgComponent extends Component
{
    /**
     * @var string
     */
    protected $element = 'img';

    /**
     * @var string
     */
    protected string $view = 'small.single-tag';

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
