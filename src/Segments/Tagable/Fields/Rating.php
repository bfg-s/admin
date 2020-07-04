<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Icon
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Rating extends Input
{
    /**
     * @var string
     */
    protected $icon = null;

    /**
     * @var bool
     */
    protected $form_control = false;

    /**
     * @var \string[][]
     */
    protected $params = [
        [
            'data-load' => 'rating'
        ]
    ];

    /**
     * @var array
     */
    protected $data = [
        'animate' => 'true',
        'step' => '1',
        'show-clear' => 'false',
        'show-caption' => 'false',
        'size' => 'xs'
    ];

    /**
     * @var int
     */
    protected $value = 0;

    /**
     * @param  int  $value
     * @param  string|null  $message
     * @return Rating
     */
    public function min(int $value, string $message = null)
    {
        $this->data['min'] = $value;

        if ($value == 0) {

            $this->data['show-clear'] = 'true';
        }

        return parent::min($value);
    }

    /**
     * @param  int  $value
     * @param  string|null  $message
     * @return Rating
     */
    public function max(int $value, string $message = null)
    {
        $this->data['max'] = $value;

        return parent::max($value);
    }

    /**
     * @param int|double $step
     * @return $this
     */
    public function step($step)
    {
        $this->data['step'] = $step;

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeXl()
    {
        $this->data['size'] = 'xl';

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeLg()
    {
        $this->data['size'] = 'lg';

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeMd()
    {
        $this->data['size'] = 'md';

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeSm()
    {
        $this->data['size'] = 'sm';

        return $this;
    }

    /**
     * @return $this
     */
    public function sizeXs()
    {
        $this->data['size'] = 'xs';

        return $this;
    }

    /**
     * @return $this
     */
    public function readonly()
    {
        $this->data['readonly'] = 'true';

        return $this;
    }

    /**
     * @return $this
     */
    public function disabled()
    {
        $this->data['disabled'] = 'disabled';

        return parent::disabled();
    }

    /**
     * @param  int  $stars
     * @return Rating
     */
    public function stars(int $stars)
    {
        $this->data['stars'] = $stars;

        return parent::disabled();
    }

    /**
     * @return $this
     */
    public function showCaption()
    {
        $this->data['show-caption'] = 'true';

        return $this;
    }
}