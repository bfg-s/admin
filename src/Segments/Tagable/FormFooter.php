<?php

namespace Lar\LteAdmin\Segments\Tagable;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 */
class FormFooter extends \Lar\LteAdmin\Components\FormFooter {

    /**
     * FormFooter constructor.
     * @param  bool  $nav_redirect
     * @param  mixed  ...$params
     */
    public function __construct($nav_redirect = true, ...$params)
    {
        parent::__construct($nav_redirect, $params);

        if (Form::$last_id) {

            $this->setFormId(Form::$last_id);
        }

        $this->createFooter();
    }
}