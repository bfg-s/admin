<?php

declare(strict_types=1);

namespace Admin\Interfaces;

use Admin\Core\Delegate;
use Admin\Page;

interface ControllerContainerInterface
{
    /**
     * @param  Page  $page
     * @param  Delegate[]  $delegates
     */
    public static function registrationInToContainer(Page $page, array $delegates = []);
}
