<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Email.
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Email extends Input
{
    /**
     * @var string
     */
    protected $type = 'email';

    /**
     * @var string
     */
    protected $icon = 'fas fa-envelope';

    /**
     * After construct event.
     */
    protected function after_construct()
    {
        $this->email();
    }
}
