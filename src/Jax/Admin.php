<?php

namespace Admin\Jax;

use Cookie;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Admin\Components\LiveComponent;
use Admin\Components\ModalComponent;
use Throwable;

class Admin extends AdminExecutor
{
    /**
     * @var array
     */
    public static array $callbacks = [

    ];
    /**
     * @var int
     */
    protected static $i = 0;

    public function load_lives()
    {
        $this->refererEmit();

        $result_areas = [];

        foreach (LiveComponent::$list as $area => $item) {
            $content = $item->getRenderContent();
            $result_areas[$area] = [
                'hash' => sha1($content),
                'content' => $content,
            ];
        }

        return $result_areas;
    }
}
