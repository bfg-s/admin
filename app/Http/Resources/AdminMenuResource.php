<?php

namespace Admin\Http\Resources;

use Admin\Models\AdminPage;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AdminMenuResource
 * @package Admin\Http\Resources
 * @mixin AdminPage
 */
class AdminMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $route = \Route::has($this->action) ? $this->action : null;

        $return = [
            'id' => $this->id,
            'order' => $this->order,
            'icon' => $this->icon,
            'title' => __($this->title),
            'description' => __($this->description),
            'action' => $route ? route($this->action) : $this->action,
            'route' => $route,
            'type' => $this->type,
            'target' => $this->target,
        ];

        if ($this->relationLoaded('childs')) {

            $return['childs'] = AdminMenuResource::collection($this->childs);
        }

        if ($this->relationLoaded('parent')) {

            $return['parent'] = $this->parent ? AdminMenuResource::make($this->parent) : null;
        }

        return $return;
    }
}
