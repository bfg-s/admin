<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Admin panel template component.
 */
class TemplateComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'template';

    /**
     * TemplateComponent constructor.
     *
     * @param  string  $id
     * @param ...$delegates
     */
    public function __construct(
        public string $id,
        ...$delegates
    ) {
        parent::__construct($delegates);

        $this->setDatas([
            'tpl' => $this->id
        ]);
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return string[]
     */
    protected function viewData(): array
    {
        return [
            'id' => $this->id
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
    }
}
