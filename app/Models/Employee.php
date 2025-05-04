<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Employee extends Model
{
    public function person() : HasOne
    {
        return $this->hasOne(Person::class);
    }
    public function salaries() : HasMany
    {
        return $this->hasMany(Salary::class);
    }

}
