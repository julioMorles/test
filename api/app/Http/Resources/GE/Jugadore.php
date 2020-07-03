<?php

namespace App\Http\Resources\GE;

use Illuminate\Http\Resources\Json\JsonResource;

class Jugadore extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
