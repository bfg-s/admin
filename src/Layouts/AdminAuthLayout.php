<?php

namespace Admin\Layouts;

use Exception;

class AdminAuthLayout extends AdminBase
{
    /**
     * Protected variable Name.
     *
     * @var string
     */
    protected $name = 'admin_auth_layout';

    /**
     * @var string
     */
    protected $pjax = 'admin-login-container';

    /**
     * AdminAuthLayout constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->body->addClass('hold-transition login-page');
        $this->body->attr('id', 'admin-login-container');
    }
}
