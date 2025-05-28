<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
    ];
    protected $casts = [
        'name' => 'string',
    ];

    public function subcategories() : HasMany
    {
        return $this->hasMany(SubCategory::class);
    }
}
