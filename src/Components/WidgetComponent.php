<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Dashboard widget component of the admin panel.
 *
 * @mixin \Admin\Components\PageComponents
 */
class WidgetComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'content-only';

    /**
     * Call trap for render first level components.
     *
     * @param $name
     * @param $arguments
     * @return $this|\Admin\Components\Component|\Admin\Components\FormComponent|\Admin\Components\InputGroupComponent|bool|mixed|string|null
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        parent::__call($name, $arguments);

        return $this;
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
