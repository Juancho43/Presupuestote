<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Supplier extends Model
{
    public function person() : HasOne
    {
        return $this->hasOne(Person::class);
    }
}
