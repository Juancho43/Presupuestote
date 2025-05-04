<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    public function person() : HasOne
    {
        return $this->hasOne(Person::class);
    }
    public function budgets() : HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
