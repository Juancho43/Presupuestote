<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'notes',
        'balance',
        'person_id',
    ];
    protected $casts = [
        'balance' => 'decimal:2',
        'notes' => 'string',
    ];
    public function person() : BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
    public function invoice() : HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
