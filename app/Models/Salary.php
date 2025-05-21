<?php

namespace App\Models;

use App\States\PaymentState\PaymentState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
      'amount',
      'date',
      'active',
      'payment_status',
    ];

    protected $casts = [
        'active' => 'boolean',
        'date' => 'date',
        'amount' => 'decimal:2',
        'payment_status' => PaymentState::class
    ];
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
    public function calculateDebt()
    {
        return $this->amount - $this->payments->sum('amount');
    }
}
