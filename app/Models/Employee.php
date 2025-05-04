<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'salary',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];
    public function person() : BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
    public function salaries() : HasMany
    {
        return $this->hasMany(Salary::class);
    }

}
