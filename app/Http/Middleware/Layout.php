<?php

namespace Admin\Http\Middleware;

use Admin\Layouts\DefaultLayout;
use Admin\Layouts\LteLayout;
use Bfg\Layout\Middleware\LayoutMiddleware;

/**
 * Class Layout
 * @package Admin\Http\Middleware
 */
class Layout extends LayoutMiddleware
{
    /**
     * @var string
     */
    protected static string $theme = 'default';

    /**
     * @var string
     */
    protected static string $color = 'white';

    /**
     * @var array
     */
    protected static array $themes = [
        'default' => DefaultLayout::class,
        'lte' => LteLayout::class,
    ];

    /**
     * Internal override for middleware children
     * @param  string  $sign
     * @return bool
     */
    protected function checkClass(string $sign)
    {
        return isset(static::$themes[static::$theme]) ?
            static::$themes[static::$theme] :
            static::$themes['default'];
    }

    /**
     * Get current theme slug
     * @return string
     */
    public static function theme()
    {
        return static::$theme;
    }

    /**
     * Set current theme for admin
     * @param  string  $slug
     */
    public static function setTheme(string $slug)
    {
        if (isset(static::$themes[$slug])) {

            static::$theme = static::$themes[$slug];
        }
    }

    /**
     * Register a new admin layout theme
     * @param  string  $slug
     * @param  string  $class
     */
    public static function registerTheme(string $slug, string $class)
    {
        static::$themes[$slug] = $class;
    }

    /**
     * Make a black color schema
     */
    public static function makeBlack()
    {
        static::$color = 'black';
    }
}
