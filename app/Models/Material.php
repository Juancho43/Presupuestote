<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'color',
        'brand',
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'color' => 'string',
        'brand' => 'string',
    ];

    public function subcategory():HasOne
    {
        return $this->HasOne(Subcategory::class);
    }

    public function work():BelongsToMany
    {
        return $this->BelongsToMany(Work::class);
    }
    public function invoice() : BelongsToMany
    {
        return $this->BelongsToMany(Invoice::class);
    }
    public function prices() : HasMany
    {
        return $this->hasMany(Price::class);
    }
    public function stocks() : HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function measure() : HasOne
    {
        return $this->hasOne(Measure::class);
    }

}
