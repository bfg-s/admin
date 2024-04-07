<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

class FileInput extends Input
{
    /**
     * @var string
     */
    protected $type = 'file';

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'file',
    ];

    /**
     * After construct event.
     */
    protected function after_construct(): void
    {
        if ($this->name && str_ends_with($this->name, '[]')) {

            $this->attr('multiple', true);
        }
    }

    /**
     * @param  mixed  ...$exts
     * @return $this
     */
    public function exts(...$exts): static
    {
        if (!isset($this->data['exts'])) {
            $this->data['exts'] = implode('|', $exts);
        } else {
            $this->data['exts'] .= implode('|', $exts);
        }

        return $this;
    }
}
