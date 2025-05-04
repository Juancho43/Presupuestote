<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable=[
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function person() : HasOne
    {
        return $this->hasOne(Person::class);
    }
    public function budgets() : HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
