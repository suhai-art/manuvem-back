<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BudgetItem extends Pivot
{
    use HasUuids;

    protected $table = 'budget_item';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'budget_id',
        'item_id',
        'name_snapshot',
        'unit_price',
        'quantity',
    ];
}
