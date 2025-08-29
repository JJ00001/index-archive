<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndexProvider extends Model
{

    protected $fillable = [
        'name',
        'shorthand',
        'description',
        'website',
    ];

    public function indices(): HasMany
    {
        return $this->hasMany(Index::class);
    }
}
