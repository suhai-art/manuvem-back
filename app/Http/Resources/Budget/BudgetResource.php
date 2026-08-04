<?php

namespace App\Http\Resources\Budget;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Enum\BudgetEnumsResource;
use App\Http\Resources\Client\ClientResource;

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
            'total_amount'  => [
                'value'     => $this->total_amount,
                'label'     => 'R$' . number_format($this->total_amount / 100, 2, ',', '.'),
            ],
            'created_at'    => $this->created_at->format('d/m/Y'),
            'updated_at'    => $this->updated_at->format('d/m/Y'),
            'client'        => ClientResource::make($this->whenLoaded('client')),
        ];
    }
}
