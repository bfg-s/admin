<?php

namespace Admin\Components\Fields;

class EmailField extends InputField
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
