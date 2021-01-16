<?php

namespace Admin\Extension\Interfaces;

/**
 * Interface ExtensionInterface
 * @package Admin\Extension\Interfaces
 */
interface ExtensionInterface {

    /**
     * @return void
     */
    public function handle(): void;
}