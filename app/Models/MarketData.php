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
        'index_holding_id',
    ];

    public function indexHolding(): BelongsTo
    {
        return $this->belongsTo(IndexHolding::class);
    }

    // TODO remove and refactor raw SQL statements to use eloquent
    protected function weight(): Attribute
    {
        return Attribute::make(fn(float $value) => round($value, 3));
    }

    public static function maxDate(): mixed
    {
        return self::max('date');
    }
}
