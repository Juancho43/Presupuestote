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

    public function subcategory():BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function works():BelongsToMany
    {
        return $this->BelongsToMany(Work::class,'material_work');
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

    public function measure() : BelongsTo
    {
        return $this->belongsTo(Measure::class);
    }

}
