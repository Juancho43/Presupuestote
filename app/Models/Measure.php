<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Measure extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation',
        'unit' // consider to eliminate this field
    ];
    protected $casts = [
        'name' => 'string',
        'abbreviation' => 'string',
        'unit' => 'decimal:2'
    ];

    public function materials():HasMany
    {
        return $this->hasMany(Material::class);
    }
}
