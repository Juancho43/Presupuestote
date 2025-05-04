<?php

namespace App\Models;

use App\Enums\WorkStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\SoftDeletes;

class Work extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order',
        'name',
        'notes',
        'estimated_time',
        'dead_line',
        'cost',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'name' => 'string',
        'notes' => 'string',
        'estimated_time' => 'integer',
        'dead_line' => 'date',
        'cost' => 'decimal:2',
        'status' => WorkStatus::class,
    ];

    public function budget() : BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

   public function materials() : BelongsToMany
   {
       return $this->belongsToMany(Material::class)->withPivot('quantity')->withTimestamps();
   }
}
