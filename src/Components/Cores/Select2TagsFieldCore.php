<?php

namespace Admin\Components\Cores;

use Lar\Layout\Tags\SELECT;

class Select2TagsFieldCore extends SELECT
{
    /**
     * @var string[]
     */
    protected $props = [
        'multiple' => 'multiple',
    ];
    /**
     * @var mixed|null
     */
    private $value;
    /**
     * @var array
     */
    private $options;

    /**
     * Col constructor.
     * @param  array  $options
     * @param  mixed  $value
     * @param  mixed  ...$params
     */
    public function __construct($options, ...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->setDatas(['tags' => 'true']);
        $this->options = $options;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValues($value)
    {
        if (!$this->hasAttribute('value')) {
            $this->value = $value;
        } else {
            $this->value = $this->getValue();
            $this->removeAttribute('value');
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
                $key = array_search($item, $this->options);
                if ($key !== false) {
                    unset($this->options[$key]);
                }
                $this->option($item)
                    ->setValue((string) $item)
                    ->setSelected();
            }
        }

        foreach ($this->options as $option) {
            $this->option($option)
                ->setValue((string) $option);
        }

        return $this;
    }
}
