<?php

namespace Lar\LteAdmin\Layouts;

use Exception;

class LteAuthLayout extends LteBase
{
    /**
     * Protected variable Name.
     *
     * @var string
     */
    protected $name = 'lte_auth_layout';

    /**
     * @var string
     */
    protected $pjax = 'lte-login-container';

    /**
     * LteAuthLayout constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->body->addClass('hold-transition login-page');
        $this->body->attr('id', 'lte-login-container');
    }
}
