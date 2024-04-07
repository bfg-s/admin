<?php

declare(strict_types=1);

namespace Admin\Interfaces;

interface ActionWorkExtensionInterface
{
    /**
     * @return void
     */
    public function handle(): void;
}
