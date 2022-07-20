<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cep extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
