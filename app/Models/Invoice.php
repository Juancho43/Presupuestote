<?php

namespace App\Models;

use App\States\PaymentState\PaymentState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasStates;

    protected $fillable = [
        'date',
        'payment_status',
        'total',
        'supplier_id',

    ];
    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
        'payment_status' => PaymentState::class
    ];

  public function materials(): BelongsToMany
  {
     return $this->belongsToMany(Material::class)
                 ->withPivot(['quantity', 'price_id', 'stock_id'])
                 ->withTimestamps()
                 ->with(['latestPrice']);
  }
    public function supplier():Belongsto
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
    public function calculateDebt()
    {
        return $this->total - $this->payments->sum('amount');
    }

    private function calculateTotal() : float
    {
        $total = 0;
        foreach ($this->materials as $material) {
            $total += $material->pivot_price * $material->pivot->quantity;
        }
        return $total;
    }
    public function updateTotal() : Invoice
    {
        $this->total = $this->calculateTotal();
        $this->save();
        $this->supplier->updateBalance();
        return $this;
    }
}
