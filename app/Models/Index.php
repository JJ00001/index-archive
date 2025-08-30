<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Index extends Model
{

    protected $fillable = [
        'name',
        'index_provider_id',
        'description',
        'currency',
    ];

    public function dataSource(): HasOne
    {
        return $this->hasOne(DataSource::class);
    }

    public function indexHoldings(): HasMany
    {
        return $this->hasMany(IndexHolding::class);
    }

    public function indexProvider(): BelongsTo
    {
        return $this->belongsTo(IndexProvider::class);
    }

}
