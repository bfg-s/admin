<?php

namespace Lar\LteAdmin\Core;

use Lar\LteAdmin\ExtendProvider;
use Lar\LteAdmin\Interfaces\NavigateInterface;

/**
 * Class InstallExtensionProvider
 * @package Lar\LteAdmin\Core
 */
class NavigatorExtensionProvider {

    /**
     * @var NavigateInterface
     */
    public $navigate;

    /**
     * @var ExtendProvider
     */
    public $provider;

    /**
     * NavigatorExtensionProvider constructor.
     * @param  NavigateInterface  $navigate
     * @param  ExtendProvider  $provider
     */
    public function __construct(NavigateInterface $navigate, ExtendProvider $provider)
    {
        $this->navigate = $navigate;
        $this->provider = $provider;
    }
}