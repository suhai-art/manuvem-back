<?php

namespace App\Http\Resources\Enum;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetEnumsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'value' => $this->resource->value,
            'label' => $this->resource->label(),
        ];
    }
}
