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
    protected $table = 'sub_categories';
    protected $fillable = [
        'name',
        'category_id',
    ];
    protected $casts = [
        'name' => 'string',
    ];

    public function material() : BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'material', 'sub_category_id');
    }
    public function category() : BelongsTo
    {
     return  $this->belongsTo(Category::class);
    }
}
