<?php

declare(strict_types=1);

namespace Admin\Exceptions;

use Exception;

/**
 * This is a controller exception, thrown when the controller cannot find the component.
 */
class NotFoundComponentForControllerException extends Exception
{
    /**
     * NotFoundComponentForControllerException constructor.
     *
     * @param  string  $name
     */
    public function __construct(string $name)
    {
        parent::__construct("No found [$name] component!");
    }
}
