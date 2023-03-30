<?php

namespace Admin\Exceptions;

use Exception;

class ResourceControllerExistsException extends Exception
{
    public function __construct($model, $controller)
    {
        parent::__construct("The resource controller already exists for the [$model] model, use the \"getModel\" method in its class. In controller: ".$controller);
    }
}
