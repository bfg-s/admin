<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

/**
 * The class that is responsible for the @alpineStore blade directive
 */
class AlpineStoreBladeDirective
{
    /**
     * Generate javascript directives for one store.
     *
     * @param $name
     * @param  array  $attributes
     * @param  bool  $needleEventListener
     * @return string
     */
    public static function oneGenerator($name = null, array $attributes = [], bool $needleEventListener = true): string
    {
        if (is_array($name)) {
            return static::manyGenerator($name);
        }
        $data = static::generator($name, $attributes);

        return $needleEventListener ? "document.addEventListener('alpine:init', function () {".$data.'})' : $data;
    }

    /**
     * Generate javascript directives for several stores.
     *
     * @param  array  $stores
     * @param  bool  $needleEventListener
     * @return string
     */
    public static function manyGenerator(array $stores = [], bool $needleEventListener = true): string
    {
        $data = '';
        foreach ($stores as $name => $attributes) {
            $data .= static::generator($name, $attributes);
        }

        return $needleEventListener ? "document.addEventListener('alpine:init', function () {".$data.'})' : $data;
    }

    /**
     * Generate javascript directives.
     *
     * @param  string|null  $name
     * @param  array  $attributes
     * @return string
     */
    public static function generator(string $name = null, array $attributes = []): string
    {
        if (!$name) {
            return '';
        }

        $json = json_encode($attributes);

        return <<<JS
Alpine.store("$name", $json)
JS;
    }

    /**
     * A function is a directive that is processed by the Blade template engine.
     *
     * @param $expression
     * @return string
     */
    public static function directive($expression): string
    {
        return "<script type='text/javascript' data-exec-on-popstate><?php echo \\".static::class."::oneGenerator($expression); ?></script>";
    }
}
