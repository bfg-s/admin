<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;

/**
 * Class Container
 * @package App\LteAdmin\Segments
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