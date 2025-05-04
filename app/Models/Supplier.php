<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'notes',
        'balance',
    ];
    protected $casts = [
        'balance' => 'decimal:2',
        'notes' => 'String',
    ];
    public function person() : HasOne
    {
        return $this->hasOne(Person::class);
    }
    public function invoice() : HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
