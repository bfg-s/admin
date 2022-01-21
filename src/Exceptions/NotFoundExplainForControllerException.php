<?php

namespace Lar\LteAdmin\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class NotFoundExplainForControllerException extends Exception
{

    #[Pure] public function __construct(string $name)
    {
        parent::__construct("No explanation [$name] to expansion!");
    }
}
