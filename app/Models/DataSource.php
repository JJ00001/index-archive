<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataSource extends Model
{

    use HasFactory;

    protected $fillable = [
        'index_id',
        'base_url',
        'field_mappings',
    ];

    protected $appends = ['field_mappings'];

    public function index(): BelongsTo
    {
        return $this->belongsTo(Index::class);
    }

    protected function casts(): array
    {
        return ['field_mappings' => 'array'];
    }

    protected function fieldMappings(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? json_decode($value, true) : self::getDefaultFieldMapping(),
        );
    }

    public static function getDefaultFieldMapping(): array
    {
        return [
            'symbol' => 0,
            'name' => 1,
            'sector' => 2,
            'asset_type' => 3,
            'market_cap' => 4, // TODO Temporary fix for backwards compatibility. Remove references
            'weight_percentage' => 5,
            'isin' => 9,
            'share_price' => 11,
            'country' => 12,
            'exchange' => 13,
            'currency' => 14,
        ];
    }

}
