<?php

namespace Lar\LteAdmin\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class ShouldBeModelInControllerException extends Exception
{

    #[Pure] public function __construct()
    {
        parent::__construct('The model in the controller should be!');
    }
}
