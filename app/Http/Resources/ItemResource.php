<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the item into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                    => $this->id,
            'internal_code'         => $this->internal_code,
            'name'                  => $this->name,
            'description'           => $this->description,
            'default_unit_price'    => $this->default_unit_price,
            'created_at'            => $this->created_at->format('d/m/Y'),
            'updated_at'            => $this->updated_at->format('d/m/Y'),
        ];
    }
}
