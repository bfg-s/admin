<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Container;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Explanation;

class AccessDeniedComponent extends Container
{
    use Delegable;

    public function __construct(...$delegates)
    {
        parent::__construct(static function (DIV $div) {
            $div->alert(
                'lte.error',
                __('lte.access_denied'),
                'fas fa-exclamation-triangle'
            )->dangerType();
        });

        $this->explainForce(Explanation::new($delegates));
    }
}
