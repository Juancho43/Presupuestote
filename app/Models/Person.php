<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{
    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
