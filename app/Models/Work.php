<?php

namespace App\Models;

use App\States\PaymentState\PaymentState;
use App\States\WorkState\WorkState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Work extends Model
{

    use HasFactory;
    use SoftDeletes;
    use HasStates;

    protected $fillable = [
        'order',
        'name',
        'notes',
        'estimated_time',
        'dead_line',
        'cost',
        'state',
        'budget_id',
    ];

    protected $casts = [
        'order' => 'integer',
        'name' => 'string',
        'notes' => 'string',
        'estimated_time' => 'integer',
        'dead_line' => 'datetime',
        'cost' => 'decimal:2',
        'state' => WorkState::class,
    ];

    public function budget() : BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)
            ->withPivot('quantity', 'price_id', 'stock_id');
    }

    public function calculateCost(): float
    {
        $cost = 0;

        if ($this->materials->isEmpty()) {
            return $cost;
        }

        foreach ($this->materials as $material) {
            $price = Price::find($material->pivot->price_id);
            $cost += $material->pivot->quantity * $price->price;
        }

        return $cost;
    }

    public function updateCost()
    {
        $this->cost = $this->calculateCost();
        $this->save();
        return $this;
    }


}
