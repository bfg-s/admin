<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;

/**
 * The class that is responsible for the @reatime blade directive.
 */
class AttributeRealtimeBladeDirective
{
    /**
     * A function is a directive that is processed by the Blade template engine.
     *
     * @param $expression
     * @return string
     */
    public static function directive($expression): string
    {
        return "<?php echo ".static::class."::attributeBuild($expression); ?>";
    }

    /**
     * A function that is responsible for generating attributes.
     *
     * @param  string  $name
     * @param  int  $timeout
     * @return string
     */
    public static function attributeBuild(string $name, int $timeout = 10000): string
    {
        return 'ata-load=\''.json_encode([
            'realtime' => ['name' => $name, 'timeout' => $timeout]
        ]).'\'';
    }
}
