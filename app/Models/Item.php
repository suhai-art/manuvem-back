<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use SoftDeletes, HasUuids, HasFactory;

    protected $table = 'items';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'internal_code',
        'name',
        'description',
        'default_unit_price',
        'active',
    ];

    protected $casts = [
        'default_unit_price' => 'decimal:2',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    public function budgets()
    {
        return $this->belongsToMany(Budget::class, 'budget_item')
            ->Using(BudgetItem::class)
            ->withPivot([
                'budget_id',
                'item_id',
                'name_snapshot',
                'unit_price',
                'quantity',
            ])->withTimestamps();
    }
}
