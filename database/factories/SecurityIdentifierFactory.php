<?php

namespace Database\Factories;

use App\Domain\Securities\Models\Security;
use App\Domain\Securities\Models\SecurityIdentifier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SecurityIdentifier> */
class SecurityIdentifierFactory extends Factory
{
    /** @var class-string<SecurityIdentifier> */
    protected $model = SecurityIdentifier::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'security_id' => Security::factory(),
            'scheme' => 'isin',
            'value' => strtoupper(fake()->unique()->bothify('??##########')),
        ];
    }
}
