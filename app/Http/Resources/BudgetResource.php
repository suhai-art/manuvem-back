<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Enums\BudgetEnumsResource;

class BudgetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'client_id'     => $this->client_id,
            'status'        => BudgetEnumsResource::make($this->status),
            'total_amount'  => $this->total_amount,
            'created_at'    => $this->created_at->format('d/m/Y'),
            'updated_at'    => $this->updated_at->format('d/m/Y'),
            'client'        => ClientResource::make($this->whenLoaded('client')),
        ];
    }
}
