<?php

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
