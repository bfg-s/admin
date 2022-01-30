<?php

namespace LteAdmin\Exceptions;

use Exception;

class ResourceControllerExistsException extends Exception
{
    public function __construct($model)
    {
        parent::__construct("The resource controller already exists for the [$model] model, use the \"getModel\" method in its class.");
    }
}
