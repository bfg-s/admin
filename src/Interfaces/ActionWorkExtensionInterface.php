<?php

declare(strict_types=1);

namespace Admin\Interfaces;

/**
 * Interface of file extensions, installation deletion and navigation.
 */
interface ActionWorkExtensionInterface
{
    /**
     * Handle performed during the desired action.
     *
     * @return void
     */
    public function handle(): void;
}
