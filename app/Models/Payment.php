<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'date',
        'description',
        'payable_id',
        'payable_type',
    ];
    protected $casts = [
        'description' => 'string',
        'date' => 'datetime',
        'amount' => 'decimal:2',
        'payable_id' => 'integer',
    ];
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
