<?php

declare(strict_types=1);

namespace Admin\Exceptions;

use Exception;

/**
 * This is a repository exception, thrown when the repository cannot find the module in the controller.
 */
class ModelShouldBeInControllerException extends Exception
{
    /**
     * ModelShouldBeInControllerException constructor.
     */
    public function __construct()
    {
        parent::__construct('The model in the controller should be!');
    }
}
