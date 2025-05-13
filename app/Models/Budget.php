<?php

namespace App\Models;

use App\Enums\BudgetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'made_date',
        'description',
        'dead_line',
        'status',
        'cost',
    ];
    protected $casts = [
        'made_date' => 'date',
        'description' => 'string',
        'dead_line' => 'date',
        'status' => BudgetStatus::class,
        'cost' => 'decimal:2'
    ];

    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function works() : HasMany
    {
        return $this->hasMany(Work::class);
    }
}
