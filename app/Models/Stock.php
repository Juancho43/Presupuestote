<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'stock',
        'date',
        'material_id',
    ];
    protected $casts = [
        'stock' => 'decimal:2',
        'date' => 'date',
        'material_id' => 'integer',
    ];
    public function material() : BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
