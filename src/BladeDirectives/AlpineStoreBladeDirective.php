<?php

namespace Admin\BladeDirectives;

class AlpineStoreBladeDirective
{
    public static function oneGenerator($name = null, array $attributes = [], bool $needleEventListener = true)
    {
        if (is_array($name)) {
            return static::manyGenerator($name);
        }
        $data = static::generator($name, $attributes);

        return $needleEventListener ? "document.addEventListener('alpine:init', function () {".$data.'})' : $data;
    }

    public static function manyGenerator(array $stores = [], bool $needleEventListener = true)
    {
        $data = '';
        foreach ($stores as $name => $attributes) {
            $data .= static::generator($name, $attributes);
        }

        return $needleEventListener ? "document.addEventListener('alpine:init', function () {".$data.'})' : $data;
    }

    public static function generator(string $name = null, array $attributes = [])
    {
        if (!$name) {
            return '';
        }

        $json = json_encode($attributes);

        return <<<JS
Alpine.store("$name", $json)
JS;
    }

    public static function directive($expression)
    {
        return "<script type='text/javascript' data-exec-on-popstate><?php echo \\".static::class."::oneGenerator($expression); ?></script>";
    }
}
