<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'last_name',
        'address',
        'phone_number',
        'mail',
        'dni',
        'cuit',

    ];
    protected $casts = [
        'name' => 'string',
        'last_name' => 'string',
        'address' => 'string',
        'phone_number' => 'string',
        'mail' => 'string',
        'dni' => 'string',
        'cuit' => 'string',
    ];

    public function employee() : HasOne
    {
        return $this->hasOne(Employee::class);
    }
    public function supplier() : HasOne
    {
        return $this->hasOne(Supplier::class);
    }
    public function client() : HasOne
    {
        return $this->hasOne(Client::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
