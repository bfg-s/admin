<?php

namespace Lar\LteAdmin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Lar\LteAdmin\Models\LteFunction;

/**
 * Class LteFunctionResource.
 * @package Lar\LteAdmin\Resources
 * @mixin LteFunction
 */
class LteFunctionResource extends JsonResource
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
            'class' => $this->class,
            'description' => lang_in_text($this->description),
            'roles' => LteRoleResource::collection($this->roles)->toArray($request),
        ];
    }
}
