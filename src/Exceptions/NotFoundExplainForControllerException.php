<?php

declare(strict_types=1);

namespace Admin\Exceptions;

use Exception;

class NotFoundExplainForControllerException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct("No explanation [$name] to expansion!");
    }
}
