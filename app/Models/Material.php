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
        'unit_measure',
        'sub_category_id',
        'measure_id',
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'color' => 'string',
        'unit_measure' => 'float',
        'brand' => 'string',
    ];

    public function subcategory():BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
    }

    public function works(): BelongsToMany
    {
        return $this->belongsToMany(Work::class)
            ->withPivot('quantity', 'price_id', 'stock_id');
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class)
            ->withPivot('quantity', 'price_id', 'stock_id');
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
    public function getPivotPriceAttribute()
    {
        if (!$this->pivot || !$this->pivot->price_id) {
            return null;
        }

        return $this->prices->firstWhere('id', $this->pivot->price_id)?->price;
    }
    public function latestPrice() : HasOne
    {
        return $this->hasOne(Price::class)->latest();
    }

    public function latestStock() : HasOne
    {
        return $this->hasOne(Stock::class)->latest();
    }

}
