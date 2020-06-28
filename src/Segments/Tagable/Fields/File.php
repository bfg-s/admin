<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class File
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class File extends Input
{
    /**
     * @var string
     */
    protected $type = "file";

    /**
     * @var string
     */
    protected $icon = null;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'file'
    ];

    /**
     * @param  mixed  ...$exts
     * @return $this
     */
    public function exts(...$exts)
    {
        if (!isset($this->data['exts'])) {
            $this->data['exts'] = implode("|", $exts);
        } else {
            $this->data['exts'] .= implode("|", $exts);
        }

        return $this;
    }
}