<?php

namespace Admin\Delegates;

use App\Admin\Delegates\Tab;
use Admin\Components\FormComponent;
use Admin\Core\Delegator;

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
