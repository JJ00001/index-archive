<?php

namespace Database\Factories;

use App\Domain\Sec\NPort\Models\Registrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Registrant> */
class RegistrantFactory extends Factory
{
    /** @var class-string<Registrant> */
    protected $model = Registrant::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'cik' => fake()->unique()->numerify('##########'),
            'name' => fake()->company(),
            'file_number' => fake()->numerify('811-#####'),
            'lei' => strtoupper(fake()->bothify('??????????????????##')),
            'address1' => fake()->streetAddress(),
            'address2' => null,
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'country' => 'US',
            'zip' => fake()->postcode(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
