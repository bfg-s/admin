<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Admin panel template area component.
 */
class TemplateAreaComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'template-area';

    /**
     * TemplateAreaComponent constructor.
     *
     * @param  string  $id
     * @param  bool  $autoload
     * @param ...$delegates
     */
    public function __construct(
        public string $id,
        public bool $autoload = false,
        ...$delegates
    ) {
        parent::__construct($delegates);

        $this->setDatas([
            'tpl' => $this->id
        ]);

        if ($autoload) {
            $this->setDatas([
                'load' => 'tpl::replaceTo',
                'load-params' => $this->id,
            ]);
        }
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
