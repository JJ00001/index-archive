<?php

namespace App\Domain\Sec\NPort\Models;

use Database\Factories\RegistrantFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(RegistrantFactory::class)]
class Registrant extends Model
{
    use HasFactory;

    protected $table = 'nport_registrants';

    protected $fillable = [
        'cik',
        'name',
        'file_number',
        'lei',
        'address1',
        'address2',
        'city',
        'state',
        'country',
        'zip',
        'phone',
    ];

    /** @return HasMany<Fund, $this> */
    public function funds(): HasMany
    {
        return $this->hasMany(Fund::class, 'registrant_id');
    }

    /** @return HasMany<Filing, $this> */
    public function filings(): HasMany
    {
        return $this->hasMany(Filing::class, 'registrant_id');
    }
}
