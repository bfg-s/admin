<?php

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
        return "<?php echo " . static::class . "::attributesBuild($expression); ?>";
    }

    /**
     * @param  array  $arrayOfAttributes
     * @return string
     */
    public static function attributesBuild(array $arrayOfAttributes): string
    {
        $html = [];
        foreach ($arrayOfAttributes as $key => $val) {
            if ($val instanceof Renderable) {
                $val = $val->render();
            }
            if (is_array($val)) {
                $key = ":$key";
            } else if ($val instanceof Arrayable) {
                $val = $val->toArray();
                $key = ":$key";
            }
            $html[] = "$key='".(is_string($val) ? $val : json_encode($val))."'";
        }
        return implode(' ', $html);
    }
}
