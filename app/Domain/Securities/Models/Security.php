<?php

namespace App\Domain\Securities\Models;

use Database\Factories\SecurityFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(SecurityFactory::class)]
class Security extends Model
{
    use HasFactory;

    /** @return HasMany<SecurityIdentifier, $this> */
    public function identifiers(): HasMany
    {
        return $this->hasMany(SecurityIdentifier::class, 'security_id');
    }
}
