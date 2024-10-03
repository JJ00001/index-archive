<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketData extends Model
{
    protected $fillable = [
        'date',
        'market_capitalization',
        'share_price',
        'weight',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected function weight(): Attribute
    {
        return Attribute::make(fn(float $value) => round($value, 3));
    }
}
