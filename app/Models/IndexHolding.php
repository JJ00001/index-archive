<?php

namespace App\Models;

use App\Models\Scopes\ActiveIndexHoldingScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([ActiveIndexHoldingScope::class])]
class IndexHolding extends Model
{

    use HasFactory;

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
