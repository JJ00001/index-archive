<?php

namespace App\Domain\Sec\NPort\Models;

use Database\Factories\PositionIdentifierFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(PositionIdentifierFactory::class)]
class PositionIdentifier extends Model
{
    use HasFactory;

    protected $table = 'nport_position_identifiers';

    protected $fillable = [
        'position_id',
        'sec_identifiers_id',
        'identifier_isin',
        'identifier_ticker',
        'other_identifier',
        'other_identifier_description',
    ];

    /** @return BelongsTo<Position, $this> */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
