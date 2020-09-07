<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;

/**
 * Class AccessDenied
 * @package Lar\LteAdmin\Segments
 */
class AccessDenied extends Container {

    /**
     * AccessDenied constructor.
     */
    public function __construct()
    {
        parent::__construct(function (DIV $div) {
            $div->alert(
                'lte.error',
                __('lte.access_denied'),
                'fas fa-exclamation-triangle'
            )->danger();
        });
    }
}