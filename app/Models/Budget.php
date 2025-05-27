<?php

namespace App\Models;

use App\States\BudgetState\BudgetState;
use App\States\PaymentState\PaymentState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Budget extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasStates;

    protected $fillable = [
        'made_date',
        'description',
        'dead_line',
        'state',
        'cost',
        'profit',
        'price',
        'client_id'
    ];
    protected $casts = [
        'made_date' => 'date',
        'description' => 'string',
        'dead_line' => 'date',
        'state' => BudgetState::class,
        'payment_status' => PaymentState::class,
        'cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'price' => 'decimal:2',
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
    public function calculateDebt()
    {
        return floatval($this->price) - $this->payments->sum('amount');
    }

    private function calculatePrice()
    {
        return $this->cost + $this->profit;
    }

    private function calculateCost()
    {
        float : $cost = 0;
        foreach ($this->works as $work) {
            $cost += $work->cost;
        }
        return $cost;
    }

    public function updateCost()
    {
        $this->cost = $this->calculateCost();
        $this->save();
        return $this;
    }

    public function updatePrice()
    {

        $this->updateCost();
        $this->price = $this->calculatePrice();
        $this->save();
        $this->client->updateBalance();
        return $this;
    }

}
