<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\SELECT;

/**
 * Class Select2
 * @package Lar\LteAdmin\Components
 */
class Select2Tags extends SELECT
{
    /**
     * @var mixed|null
     */
    private $value;

    /**
     * @var string[]
     */
    protected $props = [
        'multiple' => 'multiple'
    ];

    /**
     * Col constructor.
     * @param  array  $options
     * @param  mixed  $value
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->setDatas(['load' => 'select2', 'tags' => 'true']);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValues($value)
    {
        if (!$this->hasAttribute('value')) {

            $this->value = $value;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function makeOptions()
    {
        if (is_array($this->value)) {
            foreach ($this->value as $item) {
                $this->option($item)
                    ->setValue((string)$item)
                    ->setSelected();
            }
        }

        return $this;
    }
}