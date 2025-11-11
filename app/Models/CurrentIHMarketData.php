<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrentIHMarketData extends Model
{

    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'current_index_holding_market_data';

    protected $primaryKey = 'index_holding_id';

    protected $guarded = [];

    public function scopeForIndex(Builder $query, int $indexId): Builder
    {
        return $query->where('index_id', $indexId);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function indexHolding(): BelongsTo
    {
        return $this->belongsTo(IndexHolding::class, 'index_holding_id');
    }

    protected function casts(): array
    {
        return [
            'index_id' => 'int',
            'company_id' => 'int',
            'is_active' => 'bool',
            'weight' => 'float',
            'date' => 'date',
        ];
    }

}
