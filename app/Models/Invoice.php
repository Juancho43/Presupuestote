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

    ];
    protected $casts = [
        'date' => 'date',
        'payment_status' => PaymentState::class
    ];

     public function materials(): BelongsToMany
     {
         return $this->belongsToMany(Material::class)
                     ->withPivot(['quantity', 'unit_price'])
                     ->withTimestamps();
     }

    public function supplier():Belongsto
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
