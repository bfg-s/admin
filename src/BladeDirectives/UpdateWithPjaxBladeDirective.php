<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

class UpdateWithPjaxBladeDirective
{
    /**
     * Lives ids.
     *
     * @var array
     */
    public static array $_lives = [];

    /**
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo ".static::class."::buildAttribute(); ?>";
    }

    /**
     * @return string
     */
    public static function buildAttribute(): string
    {
        $name = 'tag-'.count(static::$_lives);

        static::$_lives[] = $name;

        return "data-update-with-pjax=\"{$name}\"";
    }
}
