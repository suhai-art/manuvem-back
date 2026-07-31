<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\BudgetStatus;

class Budget extends Model
{
    use SoftDeletes, HasUuids, HasFactory;

    protected $table = 'budgets';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'status',
        'total_amount',
    ];

    protected $casts = [
        'status'        => BudgetStatus::class,
        'total_amount'  => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
