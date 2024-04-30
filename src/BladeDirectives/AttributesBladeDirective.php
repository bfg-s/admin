<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;

class AttributesBladeDirective
{
    /**
     * @param $expression
     * @return string
     */
    public static function directive($expression): string
    {
        return "<?php echo ".static::class."::attributesBuild($expression); ?>";
    }

    /**
     * @param  array  $arrayOfAttributes
     * @return string
     */
    public static function attributesBuild(array $arrayOfAttributes): string
    {
        $html = [];
        foreach ($arrayOfAttributes as $key => $val) {
            if (!is_string($val) && is_callable($val)) {
                $val = call_user_func($val, $key);
            }
            if ($val instanceof Renderable) {
                $val = $val->render();
            }
            if ($val instanceof Arrayable) {
                $val = $val->toArray();
            }
            if (is_array($val)) {
                $key = ":$key";
            }
            $html[] = "$key='".(is_string($val) ? $val : json_encode($val))."'";
        }
        return implode(' ', $html);
    }
}
