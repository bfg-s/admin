<?php

namespace Admin\Exceptions;

use Exception;

class ShouldBeModelInControllerException extends Exception
{
    public function __construct()
    {
        parent::__construct('The model in the controller should be!');
    }
}
