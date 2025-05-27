<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable=[
        'balance',
        'person_id',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function person() : BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
    public function budgets() : HasMany
    {
        return $this->hasMany(Budget::class);
    }

    private function calculateBalance(): float
    {
        $balance = 0;
        foreach ($this->budgets as $budget) {
            $balance += $budget->calculateDebt();
        }
        return $balance;
    }

    public function updateBalance()
    {
        $this->balance = $this->calculateBalance();
        $this->save();
        return $this->balance;
    }


}
