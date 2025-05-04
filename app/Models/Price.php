<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'price',
        'date',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'date' => 'date',
    ];
    public function material() : BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

}
