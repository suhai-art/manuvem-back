<?php

namespace App\Http\Resources\Budget;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'internal_code'         => $this->internal_code,
            'name'                  => $this->pivot->name_snapshot,
            'unit_price'  => [
                'value'     => $this->privot->unit_price,
                'label'     => 'R$' . number_format($this->privot->unit_price / 100, 2, ',', '.'),
            ],
            'quantity' => $this->pivot->quantity,
        ];
    }
}
