<?php

namespace Lar\LteAdmin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Lar\LteAdmin\Models\LteRole;

/**
 * Class LteRoleResource.
 * @package Lar\LteAdmin\Resources
 * @mixin LteRole
 */
class LteRoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
        ];
    }
}
