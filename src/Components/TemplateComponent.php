<?php

declare(strict_types=1);

namespace Admin\Components;

use Closure;
use Admin\Traits\FontAwesome;
use Admin\Traits\TypesTrait;

class TemplateComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'template';

    /**
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
