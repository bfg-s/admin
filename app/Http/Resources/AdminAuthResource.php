<?php

namespace Admin\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AdminAuthResource
 * @package Admin\Http\Resources
 */
class AdminAuthResource extends JsonResource
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
            'message' => $this->result ? 'Success auth' : 'Error of auth'
        ];
    }
}
