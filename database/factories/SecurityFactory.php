<?php

namespace Database\Factories;

use App\Domain\Securities\Models\Security;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Security> */
class SecurityFactory extends Factory
{
    /** @var class-string<Security> */
    protected $model = Security::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
