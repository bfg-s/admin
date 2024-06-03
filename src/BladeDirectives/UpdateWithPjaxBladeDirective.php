<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

/**
 * The class is responsible for the blade directive @updateWithPjax.
 */
class UpdateWithPjaxBladeDirective
{
    /**
     * List of live tags that are updated.
     *
     * @var array
     */
    public static array $_lives = [];

    /**
     * A function is a directive that is processed by the Blade template engine.
     *
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo ".static::class."::buildAttribute(); ?>";
    }

    /**
     * A function that is responsible for generating data attribute.
     *
     * @return string
     */
    public static function buildAttribute(): string
    {
        $name = 'tag-'.count(static::$_lives);

        static::$_lives[] = $name;

        return "data-update-with-pjax=\"{$name}\"";
    }
}
