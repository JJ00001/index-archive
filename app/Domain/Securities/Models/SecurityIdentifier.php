<?php

namespace App\Domain\Securities\Models;

use Database\Factories\SecurityIdentifierFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(SecurityIdentifierFactory::class)]
class SecurityIdentifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'security_id',
        'scheme',
        'value',
    ];

    /** @return BelongsTo<Security, $this> */
    public function security(): BelongsTo
    {
        return $this->belongsTo(Security::class, 'security_id');
    }
}
