<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IndexHolding extends Model
{

    protected $fillable = [
        'index_id',
        'company_id',
    ];

    public function index(): BelongsTo
    {
        return $this->belongsTo(Index::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function marketData(): HasMany
    {
        return $this->hasMany(MarketData::class);
    }
}
