<?php

namespace Lar\LteAdmin\Interfaces;

use Lar\LteAdmin\Core\Delegate;
use Lar\LteAdmin\Page;

interface ControllerContainerInterface
{
    /**
     * @param  Page  $page
     * @param  Delegate[]  $delegates
     */
    public static function registrationInToContainer(Page $page, array $delegates = []);
}
