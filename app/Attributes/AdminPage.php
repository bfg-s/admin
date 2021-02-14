<?php

namespace Admin\Attributes;

use Attribute;
use Bfg\Route\Attributes\Route;

/**
 * Class AdminPage
 * @package Admin\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class AdminPage extends Route
{
    /**
     * AdminPage constructor.
     * @param  string  $uri
     * @param  string|null  $name
     * @param  array|string  $middleware
     */
    public function __construct(string $uri, ?string $name = null, array|string $middleware = [])
    {
        parent::__construct(
            method: ['GET', 'HEAD', 'POST'],
            uri: $uri,
            name: $name,
            middleware: $middleware
        );
    }
}