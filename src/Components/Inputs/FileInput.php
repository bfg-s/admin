<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input the admin panel to select a file.
 */
class FileInput extends Input
{
    /**
     * HTML attribute of the input type.
     *
     * @var string
     */
    protected $type = 'file';

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'file',
    ];

    /**
     * List file extensions that are available for selection.
     *
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

    /**
     * After construct event.
     *
     * @return void
     */
    protected function after_construct(): void
    {
        if ($this->name && str_ends_with($this->name, '[]')) {
            $this->attr('multiple', true);
        }
    }
}
