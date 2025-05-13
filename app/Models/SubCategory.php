<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'subcategories';
    protected $fillable = [
        'name',
    ];
    protected $casts = [
        'name' => 'string',
    ];

    public function material() : BelongsToMany
    {
        return $this->belongsToMany(Material::class);
    }
    public function category() : BelongsTo
    {
     return  $this->belongsTo(Category::class);
    }
}
