<?php

declare(strict_types=1);

namespace Admin\Components;

class TemplateAreaComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'template-area';

    /**
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
     * @return string[]
     */
    protected function viewData(): array
    {
        return [
            'id' => $this->id
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
    }
}
