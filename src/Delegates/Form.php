<?php

namespace LteAdmin\Delegates;

use App\Admin\Delegates\Tab;
use LteAdmin\Components\FormComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin FormComponent
 */
class Form extends Delegator
{
    protected $class = FormComponent::class;

    public function tabGeneral(...$delegates): array
    {
        $tab = new Tab();

        return [
            $this->tab(
                $tab->title('General')->icon_globe(),
                $tab->inputInfoId(),
                [...$delegates],
                $tab->inputInfoAt(),
            )
        ];
    }
}
